<?php

namespace App\Http\Controllers\Api\V1;

use Exception;
use App\Models\Classe;
use League\Csv\Reader;
use League\Csv\Writer;
use SplTempFileObject;
use App\Models\Student;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use App\Models\AbsenceStudent;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class StudentController extends Controller
{

    public function getStudents(Request $request, Classe $classe)
    {

        if (!Auth::check()) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }

        $user = Auth::user();

        // Define the base query for selecting student and user data
        $baseQuery = Student::select(
            DB::raw('Student.id as student_id'),
            DB::raw('_User.firstName'),
            DB::raw('_User.lastName'),
            DB::raw('_User.firstNameFr'),
            DB::raw('_User.lastNameFr'),
            DB::raw('_User.firstNameEn'),
            DB::raw('_User.lastNameEn'),
            DB::raw('_User.firstNameZ'),
            DB::raw('_User.lastNameZ'),
            DB::raw('Student.num'),
        )
            ->join('_User', '_User.id', '=', 'Student.userId')
            ->where('classeId', $classe->id)
            ->whereNull('Student.deleted_at')
            ->whereNull('_User.deleted_at');

        // if the user is a student, ensure they can only access their own data
        if ($user->hasRole('student')) {

            // Add the user-specific constraint to the base query
            $studentData = $baseQuery->where('Student.userId', $user->id)->get();

            // Check if the student exists and belongs to the specified class
            // The where('classeId', $classe->id) in the base query already handles the class check.
            if ($studentData->isEmpty()) {
                // This means the user is a student, but either they are not linked to a student record, 
                // or they don't belong to the requested $classe.
                return response()->json(['message' => __('vocab.not_authorized')], 403);
            } else {
                // Return the single student in the same collection format
                return response()->json(['students' => $studentData]);
            }
        }

        // For non-student roles, fetch all students in the class using the base query
        $students = $baseQuery->get();

        return response()->json(['students' => $students]);
    }

    
    public function index(Request $request, $page_size = 40)
    {
        if (!Auth::check()) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }

        try {
            if ($request->filled("page_size"))
                $page_size = $request->input("page_size");

            // Use Eloquent's withCount and withSum for cleaner queries and to avoid GROUP BY issues.
            // The filter() method in the Student model is adjusted to handle this.
            $query = Student::withSum('absences as absences_sum_plage', 'plage')
                ->withCount('absences as absences_count')
                ->with('user')
                ->filter($request);

            return $query->paginate($page_size);
        } catch (Exception $e) {
            return response()->json(['message' => __('vocab.no_data') . $e->getMessage()], 404);
        }
    }

    public function show(Request $request, $classe, $page_size = 40)
    {
        if (!Auth::check()) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }

        try {
            if ($request->filled("page_size"))
                $page_size = $request->input("page_size");

            return Student::where('classeId', $classe->id) // Pass the ID, not the object
                ->withSum('absences as absences_sum_plage', 'plage')
                ->withCount('absences as absences_count')
                ->filter($request)
                ->paginate($page_size);
        } catch (Exception $e) {
            return response()->json(['message' => __('vocab.no_data') . $e->getMessage()], 404);
        }
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }
        // Validate the request data
        $validatedData = $request->validate([
            'num' => 'required|numeric',
            'classeId' => 'required|string',
            'userId' => 'required|string',
        ]);

        try {
            $student = DB::transaction(function () use ($validatedData) {
                if (!Auth::user()->hasRole('admin')) {
                    // Fail if the class is not owned by the current user
                    Classe::where('userId', auth()->user()->id)
                        ->findOrFail($validatedData['classeId']);
                }

                // Create a new Student object and assign the validated data
                return Student::create($validatedData);
            });

            return response()->json(['message' => __('vocab.action_success'), 'student' => $student], 201);
        } catch (Exception $exception) {
            // Catches ModelNotFoundException from findOrFail as well as other potential errors.
            return response()->json(['message' => __('vocab.task_failed') . ' ' . __('vocab.try_again') . $exception->getMessage()], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     */
    public function update(Request $request, Student $student)
    {
        if (!Auth::user()->hasRole('admin') && Auth::user()->id !== $student->classe->userId) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }
        $validatedData = $request->validate([
            'num' => 'required|numeric',
            'classeId' => 'required|string',
        ]);
        try {
            // Save the student
            $student->update($validatedData);

            return response()->json(['message' => __('vocab.update_success'), 'student' => $student], 200);
        } catch (Exception $exception) {
            return response()->json(['message' => __('vocab.update_failure') . ' ' . __('vocab.try_again') . $exception->getMessage()], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     */
    public function destroy(Student $student)
    {
        if (
            !Auth::user()->hasRole('admin') &&
            !(Auth::user()->id === $student->classe->userId)
        ) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }
        // Soft delete the student
        $student->delete();
        // Return a response
        return response()->json(['message' => __('vocab.delete_success')], 200);
    }

    public function upload(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'file' => 'required|file|mimes:csv|max:10240',
            'classeId' => 'nullable|string',
        ]);
        $not_imported = [];
        $updated = [];
        $added = [];

        try {
            // Store the file locally
            $file = $request->file('file');
            $fileName = 'students-' . Str::uuid() . '.csv';
            $filePath = $file->storeAs('imports', $fileName, 'public');
            //default classe
            if ($request->filled('classeId')) {
                $classeId = $request->input('classeId');
                $classe = Classe::where('userId', auth()->user()->id)
                    ->find($classeId);
                if (!$classe)
                    unset($classeId);
            }

            // Read the CSV file
            $csv = Reader::createFromPath(storage_path('app/public/' . $filePath), 'r');

            $csv->setHeaderOffset(0);


            // Import the students from the CSV
            foreach ($csv->getRecords() as $record) {
                $itemData = [
                    'id' => $record['id'],
                    'code' => $record['Code'],
                    'firstName' => $record['firstName'],
                    'lastName' => $record['lastName'],
                    'num' => $record['num'],
                    'imageId' => $record['imageId'],
                    'classeId' => $record['classeId'],
                ];

                if (isset($classeId) && $classeId) {
                    $itemData['classeId'] = $classeId;
                } else {
                    $classe = Classe::where('userId', auth()->user()->id)
                        ->find($itemData['classeId']);
                    if (!$classe) {
                        $not_imported[] = $itemData['id'];
                        continue;
                    }
                }

                // Create/update Student model and save it to the database
                try {
                    $student = Student::where('classeId', $itemData['classeId'])
                        ->find($itemData['id']);
                    if ($student) {
                        $student->update($itemData);
                        $updated[] = $itemData['id'];
                    } else {
                        Student::create($itemData);
                        $added[] = $itemData['id'];
                    }
                } catch (Exception $e) {
                    $not_imported[] = $itemData['id'];
                }
            }
            // Delete the temporary CSV file
            File::delete(storage_path('app/public/' . $filePath));
            // Return a response
            return response()->json([
                'message' => 'Import summary:' .
                    count($added) . ' added, ' .
                    count($updated) . ' updated, ' .
                    count($not_imported) . ' not imported.',
                'not_imported' => $not_imported,
                'updated' => $updated,
                'added' => $added,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Import failed.' . $e->getMessage()
            ], 404);
        }
    }

    public function download(Request $request)
    {
        try {
            $students = [];
            if ($request->filled('classeId')) {
                $classeId = $request->input('classeId');
                $classe = Classe::where('userId', auth()->user()->id)
                    ->find($classeId);
                if ($classe)
                    $students = $classe->students->get();
            } else {
                $students = Student::whereHas('classe', function ($classe) {
                    $classe->where('userId', auth()->user()->id);
                })->get();
            }

            // Generate the filename for the exported file
            $fileName = 'students_' . time() . '.csv';


            $csv = Writer::createFromFileObject(new SplTempFileObject());

            // Add headers to the CSV
            $headers = ['id', 'Code', 'firstName', 'lastName', 'firstNameFr', 'lastNameFr', 'firstNameEn', 'lastNameEn', 'firstNameZ', 'lastNameZ', 'num', 'imageId', 'classeId'];
            $csv->insertOne($headers);

            // Add students to the CSV
            foreach ($students as $student) {
                $user = $student->user; // Eager load user to avoid N+1
                $itemData = [
                    $student->id,
                    $user->code,
                    $user->firstName,
                    $user->lastName,
                    $user->firstNameFr,
                    $user->lastNameFr,
                    $user->firstNameEn,
                    $user->lastNameEn,
                    $user->firstNameZ,
                    $user->lastNameZ,
                    $student->num, // Corrected order
                    $user->imageId,
                    $student->classeId,
                ];
                $csv->insertOne($itemData);
            }
            // Set the response headers
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename=' . $fileName,
            ];

            // Return the CSV file as a downloadable response
            return Response::make($csv->toString(), 200, $headers);
        } catch (Exception $exception) {
            return response()->json(['message' => 'Downloading Failed. ' . $exception->getMessage()], 404);
        }
    }

    public function move(Request $request, Student $student, Classe $classe)
    {

        try {
            $user = Auth::user();
            if ($student->classe->userId == $user->id && $classe->userId == $user->id) {
                $student->update(['classeId' => $classe->id]);
                return response()->json(['message' => 'Moved successfully', 'student' => $student], 200);
            }
            return response()->json(['message' => 'Not moved. Not your classes!'], 404);
        } catch (Exception $exception) {
            return response()->json(['message' => 'Not moved. ' . $exception->getMessage()], 404);
        }
    }
}
