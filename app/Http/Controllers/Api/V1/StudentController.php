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
    /**
     * Display a listing of the resource.
     *
     * @param int $page_size
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $page_size = 40)
    {
        try {
            if ($request->filled("page_size"))
                $page_size = $request->input("page_size");
            /*
            $query = Student::withSum('absences','plage')
            ->withCount('absences')
            ->groupByRaw('"Student".id,"Student".userId,"Student".classeId,"Student".num')
            ->filter($request);
            */
            $query = Student::query()
                ->select(
                    DB::raw('"Student".*'),
                    DB::raw('sum("AbsenceStudent".plage) as absences_sum_plage'),
                    DB::raw('count(AbsenceStudent.studentId) as absences_count ')
                )
                ->leftJoin('AbsenceStudent', 'Student.id', '=', 'AbsenceStudent.studentId')
                ->groupBy('Student.id', 'Student.userId', 'Student.classeId', 'Student.num')
                ->filter($request);

            //dd($query->toSql(), $query->getBindings());

            return $query->paginate($page_size);
        } catch (Exception $e) {
            dd($e->getMessage());
            return response()->json(['message' => 'غير متوفر. ' . $e->getMessage()], 404);
        }
    }

    public function show(Request $request, $classe, $page_size = 40)
    {
        try {
            if ($request->filled("page_size"))
                $page_size = $request->input("page_size");

            return Student::where('classeId', $classe->id)
                ->withSum('absences', 'plage')
                ->withCount('absences')
                ->groupByRaw('"Student".id,"Student".userId,"Student".classeId,"Student".num')
                ->filter($request)
                ->paginate($page_size);
        } catch (Exception $e) {
            return response()->json(['message' => 'غير متوفر. ' . $e->getMessage()], 404);
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
            return response()->json(['message' => 'غير مسموح، المرجو تسجيل الدخول'], 403);
        }
        // Validate the request data
        $validatedData = $request->validate([
            'num' => 'required|numeric',
            'classeId' => 'required|string',
            'userId' => 'required|string',
        ]);


        try {
            if (!Auth::user()->hasRole('admin') && $request->filled('classeId')) {
                $classeId = $validatedData['classeId'];
                //fail if the class is not owned by current user
                $classe = Classe::where('userId', auth()->user()->id)
                    ->findOrFail($classeId);
            }

            $validatedData['classeId'] = $classeId;
            // Create a new Student object and assign the validated data
            $student = Student::create($validatedData);
            // Return a response
            return response()->json(['message' => 'تمت الاضافة بنجاح', 'student' => $student], 201);
        } catch (Exception $exception) {
            return response()->json(['message' => 'العملية لم تتم بنجاح، المرجو اعادة المحاولة' . $exception->getMessage()], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     */
    public function update(Request $request, Student $student)
    {
        if (!Auth::user()->hasRole('admin') && Auth::user()->id !== $student->classe->userId) {
            return response()->json(['message' => 'تم الرفض، لا تمتلك التفويض الكافي للقيام بالعملية'], 403);
        }
        $validatedData = $request->validate([
            'num' => 'required|numeric',
            'classeId' => 'required|string',
        ]);
        try {
            // Save the student
            $student->update($validatedData);

            return response()->json(['message' => 'تم التعديل بنجاح', 'student' => $student], 200);
        } catch (Exception $exception) {
            return response()->json(['message' => 'لم يتم التعديل بنجاح، المرجو اعادة المحاولة' . $exception->getMessage()], 404);
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
            return response()->json(['message' => 'تم الرفض، لا تمتلك التفويض الكافي للقيام بالعملية'], 403);
        }
        // Soft delete the student
        $student->delete();
        // Return a response
        return response()->json(['message' => 'تم الحدف بنجاح'], 200);
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
            $headers = ['id', 'Code', 'firstName', 'lastName', 'num', 'imageId', 'classeId'];
            $csv->insertOne($headers);

            // Add students to the CSV
            foreach ($students as $student) {
                $itemData = [
                    $student->id,
                    $student->code,
                    $student->firstName,
                    $student->lastName,
                    $student->imageId,
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
