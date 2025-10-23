<?php

namespace App\Http\Controllers\Api\V1;

use Exception;
use App\Models\User;
use App\Models\Classe;
use App\Models\Seance;
use League\Csv\Reader;
use League\Csv\Writer;
use SplTempFileObject;

use App\Models\Student;
use App\Models\ClasseImage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Enrollment;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;

class ClasseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param int $page_size
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $page_size = 16)
    {
        if (!Auth::check()) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }
        
        try {
            if ($request->filled("page_size"))
                $page_size = $request->input("page_size");

            return Classe::filter($request)
                ->with('user')
                ->with('classeImage')
                ->with('classeImages')
                ->paginate($page_size);
        } catch (Exception $e) {
            return response()->json(['message' => __('vocab.empty') . $e->getMessage()], 404);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @param int $page_size
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $page_size = 16)

    {
        if (!Auth::check()) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }
        try {
            if ($request->filled("page_size"))
                $page_size = $request->input("page_size");

            return Classe::filter($request)
                ->where('userId', Auth::user()->id)
                ->with('classeImage')
                ->with('classeImages')
                ->paginate($page_size);
        } catch (Exception $e) {
            return response()->json(['message' => __('vocab.empty') . $e->getMessage()], 404);
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
        if (!Auth::check() || (!Auth::user()->hasRole('admin') && !Auth::user()->hasRole('manager'))) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }

        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'required|string',
            'nameFr' => 'nullable|string',
            'nameEn' => 'required|string',
            'nameZ' => 'nullable|string',
            'description' => 'nullable|string',
            'tags' => 'nullable|string',
            'published_at' => 'nullable|string',
            'addressId' => 'nullable|string',
            'imageId' => 'nullable|string',
            'categoryId' => 'nullable|string',
            'subscriptionId' => 'nullable|string',
        ]);
        try {
            // Create a new Classe object and assign the validated data
            $validatedData['userId'] = Auth::user()->id;
            $classe = Classe::create($validatedData);

            // Return a response
            return response()->json(['message' => __('vocab.action_success'), 'classe' => $classe], 201);
        } catch (Exception $exception) {
            return response()->json(['message' => __('vocab.task_failed') . ' ' . __('vocab.try_again') . $exception->getMessage()], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     */
    public function update(Request $request, Classe $classe)
    {
        if (!Auth::check()) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }
        if (!Auth::user()->hasRole('admin') && Auth::user()->id !== $classe->userId) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }

        $validatedData = $request->validate([
            'name' => 'required|string',
            'nameFr' => 'nullable|string',
            'nameEn' => 'required|string',
            'nameZ' => 'nullable|string',
            'description' => 'nullable|string',
            'tags' => 'nullable|string',
            'addressId' => 'nullable|string',
            'imageId' => 'nullable|string',
            'categoryId' => 'nullable|string',
            'subscriptionId' => 'nullable|string',
            'addressId' => 'nullable|string',
        ]);
        try {

            // Save
            $classe->update($validatedData);
            // Return the updated
            return response()->json(['message' => __('vocab.update_success'), 'classe' => $classe], 200);
        } catch (Exception $exception) {
            return response()->json(['message' => __('vocab.update_failure') . ' ' . __('vocab.try_again') . $exception->getMessage()], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     */
    public function destroy(Classe $classe)
    {
        if (!Auth::check()) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }

        if (Auth::user()->id !== $classe->userId && !Auth::user()->hasRole('admin')) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }
        // Soft delete
        DB::transaction(function () use ($classe) {
            /* ClasseImage::where('classeId',$classe->id)->delete();
            Seance::where('classeId',$classe->id)->delete();
            Student::where('classeId',$classe->id)->delete(); */
            Enrollment::where('classeId', $classe->id)->delete();
            $classe->classeImages()->delete();
            $classe->students()->delete();
            $classe->seances()->delete();
            $classe->delete();
        });
        // Return a response
        return response()->json(['message' => __('vocab.delete_success')], 200);
    }

    public function export(Classe $classe)
    {
        try {
            if (!Auth::user()->hasRole('admin') && Auth::user()->id !== $classe->userId) {
                return response()->json(['message' => __('vocab.not_authorized')], 403);
            }

            $students = $classe->students()->with('user')->get();
            $fileName = 'Classe_' . $classe->id . '_students_' . time() . '.csv';

            $csv = Writer::createFromFileObject(new SplTempFileObject());

            // Add headers matching the database schema and user fields
            $headers = ['id', 'num', 'userId', 'classeId', 'first_name', 'last_name', 'first_name_fr', 'last_name_fr', 'first_name_en', 'last_name_en', 'first_name_z', 'last_name_z', 'code', 'tag_id', 'email', 'created_at', 'updated_at'];
            $csv->insertOne($headers);

            // Add students data including user fields
            foreach ($students as $student) {
                $itemData = [
                    $student->id,
                    $student->num,
                    $student->userId,
                    $student->classeId,
                    $student->user->firstName,
                    $student->user->lastName,
                    $student->user->firstNameFr,
                    $student->user->lastNameFr,
                    $student->user->firstNameEn,
                    $student->user->lastNameEn,
                    $student->user->firstNameZ,
                    $student->user->lastNameZ,
                    $student->user->code,
                    $student->user->tag_id,
                    $student->user->email,
                    $student->created_at,
                    $student->updated_at
                ];
                $csv->insertOne($itemData);
            }

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename=' . $fileName,
            ];

            return Response::make($csv->toString(), 200, $headers);
        } catch (Exception $exception) {
            return response()->json(['message' => 'Exporting Failed. ' . $exception->getMessage()], 404);
        }
    }
    public function import(Request $request, Classe $classe)
    {
        if (Auth::user()->id !== $classe->userId && !Auth::user()->hasRole('admin')) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }

        $validatedData = $request->validate([
            'file' => 'required|file|mimes:csv|max:10240',
        ]);

        try {
            $updated = 0;
            $added = 0;
            $failed = 0;

            $fileName_summary = 'import_summary_' . $classe->id . '_' . time() . '.csv';
            $csv_summary = Writer::createFromFileObject(new SplTempFileObject());
            $headers = ['event', 'info', 'status'];
            $csv_summary->insertOne($headers);

            $file = $request->file('file');
            $fileName = 'students-' . Str::uuid() . '.csv';
            $filePath = $file->storeAs('imports', $fileName, 'public');

            $csv = Reader::createFromPath(storage_path('app/public/' . $filePath), 'r');
            $csv->setHeaderOffset(0);

            foreach ($csv->getRecords() as $record) {
                try {
                    // First create or find user
                    $user = User::firstOrCreate(
                        ['email' => $record['email']],
                        [
                            'password' => $record['password'] ?? Hash::make('password'),
                            'firstName' => $record['first_name'],
                            'lastName' => $record['last_name'],
                            'firstNameFr' => $record['first_name_fr'],
                            'lastNameFr' => $record['last_name_fr'],
                            'firstNameEn' => $record['first_name_en'],
                            'lastNameEn' => $record['last_name_en'],
                            'firstNameZ' => $record['first_name_z'],
                            'lastNameZ' => $record['last_name_z'],
                            'code' => $record['code'] ?? rand(10000, 100000),
                            'tag_id' => $record['tag_id'] ?? rand(10000, 100000),
                        ]
                    );

                    $studentData = [
                        'num' => $record['num'],
                        'userId' => $user->id,
                        'classeId' => $classe->id
                    ];

                    // Try to update existing student
                    if (isset($record['id'])) {
                        $student = Student::where('classeId', $classe->id)
                            ->find($record['id']);

                        if ($student) {
                            $student->update($studentData);
                            $csv_summary->insertOne(['update', $student->id, 'updated']);
                            $updated++;
                            continue;
                        }
                    }

                    // Create new student if update failed or no ID provided
                    $student = Student::create($studentData);
                    $csv_summary->insertOne([($record['id'] ?? 'new'), $student->id, 'added']);
                    $added++;
                } catch (Exception $e) {

                    $csv_summary->insertOne(['error', $e->getMessage(), 'failed']);
                    $failed++;
                }
            }

            File::delete(storage_path('app/public/' . $filePath));

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename=' . $fileName_summary,
                'added' => $added,
                'updated' => $updated,
                'failed' => $failed
            ];

            return Response::make($csv_summary->toString(), 200, $headers);
        } catch (Exception $e) {
            return response()->json([
                'message' => __('vocab.action_failure') . $e->getMessage()
            ], 404);
        }
    }

    // export all classes
    public function exportClasses(Request $request)
    {
        try {
            // add authentication and authorization checks
            if (!Auth::check()) {
                return response()->json(['message' => __('vocab.not_authorized')], 403);
            }

            if (Auth::user()->hasRole('admin')) {
                $classes = Classe::filter($request)
                    ->get();
            } else {
                $classes = Classe::filter($request)
                    ->where('userId', Auth::user()->id)
                    ->get();
            }


            $fileName = 'User_' . Auth::user()->id . '_classes_' . time() . '.csv';

            $csv = Writer::createFromFileObject(new SplTempFileObject());

            // Add headers matching the database schema and user fields
            $headers = ['id', 'name', 'nameFr', 'nameEn', 'nameZ', 'description', 'tags', 'imageId', 'userId', 'created_at', 'updated_at'];
            $csv->insertOne($headers);

            // Add classes data including user fields
            foreach ($classes as $classe) {
                $itemData = [
                    $classe->id,
                    $classe->name,
                    $classe->nameFr,
                    $classe->nameEn,
                    $classe->nameZ,
                    $classe->description,
                    $classe->tags,
                    $classe->imageId,
                    $classe->userId,
                    $classe->created_at,
                    $classe->updated_at
                ];
                $csv->insertOne($itemData);
            }

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename=' . $fileName,
            ];

            return Response::make($csv->toString(), 200, $headers);
        } catch (Exception $exception) {
            return response()->json(['message' => 'Exporting Failed. ' . $exception->getMessage()], 404);
        }
    }
    // import multiple classes
    public function importClasses(Request $request)
    {
        if (!Auth::check() || (!Auth::user()->hasRole('admin') && !Auth::user()->hasRole('manager'))) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }


        $validatedData = $request->validate([
            'file' => 'required|file|mimes:csv|max:10240',
        ]);

        try {
            $updated = 0;
            $added = 0;
            $failed = 0;

            $fileName_summary = 'import_classes_summary_' . time() . '.csv';
            $csv_summary = Writer::createFromFileObject(new SplTempFileObject());
            $headers = ['event', 'info', 'status'];
            $csv_summary->insertOne($headers);

            $file = $request->file('file');
            $fileName = 'classes-' . Str::uuid() . '.csv';
            $filePath = $file->storeAs('imports', $fileName, 'public');

            $csv = Reader::createFromPath(storage_path('app/public/' . $filePath), 'r');
            $csv->setHeaderOffset(0);

            foreach ($csv->getRecords() as $record) {
                try {
                    $classData = [
                        'name' => $record['name'],
                        'nameFr' => $record['nameFr'],
                        'nameEn' => $record['nameEn'],
                        'nameZ' => $record['nameZ'],
                        'description' => $record['description'] ?? null,
                        'tags' => $record['tags'] ?? null,
                        'imageId' => $record['imageId'] ?? null,
                        'userId' => Auth::user()->id
                    ];

                    if (isset($record['id'])) {
                        $class = Classe::where('userId', Auth::user()->id)
                            ->find($record['id']);

                        if ($class) {
                            $class->update($classData);
                            $csv_summary->insertOne(['update', $class->id, 'updated']);
                            $updated++;
                            continue;
                        }
                    }

                    $class = Classe::create($classData);
                    $csv_summary->insertOne(['new', $class->id, 'added']);
                    $added++;
                } catch (Exception $e) {
                    $csv_summary->insertOne(['error', $e->getMessage(), 'failed']);
                    $failed++;
                }
            }

            File::delete(storage_path('app/public/' . $filePath));

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename=' . $fileName_summary,
                'added' => $added,
                'updated' => $updated,
                'failed' => $failed
            ];

            return Response::make($csv_summary->toString(), 200, $headers);
        } catch (Exception $e) {
            return response()->json([
                'message' => __('vocab.action_failure') . $e->getMessage()
            ], 404);
        }
    }


    /**
     * Soft deletes all classe records.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyAll(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }

        if (!Auth::user()->hasRole('manager') && !Auth::user()->hasRole('admin')) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }

        // Soft delete all seances by updating the deleted_at timestamp
        Classe::filter($request)->update(['deleted_at' => now()]);

        // Return a response
        return response()->json(['message' => __('vocab.delete_success')], 200);
    }
}
