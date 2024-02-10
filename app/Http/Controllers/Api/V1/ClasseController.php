<?php

namespace App\Http\Controllers\Api\V1;

use Exception;
use App\Models\Student;
use App\Models\User;
use League\Csv\Reader;
use League\Csv\Writer;
use SplTempFileObject;
use Illuminate\Support\Facades\File;

use App\Models\Classe;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
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
        try {
            if ($request->filled("page_size"))
                $page_size = $request->input("page_size");

            return Classe::filter($request)
                ->with('user')
                ->with('classeImage')
                ->with('classeImages')
                ->paginate($page_size);
        } catch (Exception $e) {
            return response()->json(['message' => 'غير متوفر. ' . $e->getMessage()], 404);
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
        try {
            if ($request->filled("page_size"))
                $page_size = $request->input("page_size");

            return Classe::filter($request)
                ->where('userId', Auth::user()->id)
                ->with('classeImage')
                ->with('classeImages')
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
            'name' => 'required|string',
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
            return response()->json(['message' => 'تمت الاضافة بنجاح', 'classe' => $classe], 201);
        } catch (Exception $exception) {
            return response()->json(['message' => 'العملية لم تتم بنجاح، المرجو اعادة المحاولة' . $exception->getMessage()], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     */
    public function update(Request $request, Classe $classe)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'غير مسموح، المرجو تسجيل الدخول'], 403);
        }
        if (Auth::user()->id !== $classe->user->id) {
            return response()->json(['message' => 'تم الرفض، لا تمتلك التفويض الكافي للقيام بالعملية'], 403);
        }

        $validatedData = $request->validate([
            'name' => 'required|string',
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
            return response()->json(['message' => 'تم التعديل بنجاح', 'classe' => $classe], 200);
        } catch (Exception $exception) {
            return response()->json(['message' => 'لم يتم التعديل بنجاح، المرجو اعادة المحاولة' . $exception->getMessage()], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     */
    public function destroy(Classe $classe)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'غير مسموح، المرجو تسجيل الدخول'], 403);
        }

        if (Auth::user()->id !== $classe->userId && !Auth::user()->hasRole('admin')) {
            return response()->json(['message' => 'تم الرفض، لا تمتلك التفويض الكافي للقيام بالعملية'], 403);
        }
        // Soft delete
        $classe->delete();
        // Return a response
        return response()->json(['message' => 'تم الحدف بنجاح'], 200);
    }


    public function export(Classe $classe)
    {
        try {

            if (Auth::user()->id !== $classe->user->id) {
                return response()->json(['message' => 'تم الرفض، لا تمتلك التفويض الكافي للقيام بالعملية'], 403);
            }

            $students = $classe->students->get();


            // Generate the filename for the exported file
            $fileName = 'Store_' . $classe->id . '_students_' . time() . '.csv';


            $csv = Writer::createFromFileObject(new SplTempFileObject());

            // Add headers to the CSV
            $headers = ['id', 'Code', 'Title', 'Description', 'Base_Price', 'Price', 'Fee', 'Heures', 'Min_Qty', 'Max_Qty', 'Location', 'Published_At', 'Expire_At', 'Image_ID', 'Store_ID', 'Category_ID', 'Tags'];
            $csv->insertOne($headers);

            // Add students to the CSV
            foreach ($students as $student) {
                $itemData = [
                    $student->id,
                    $student->code,
                    $student->title,
                    $student->description,
                    $student->basePrice,
                    $student->price,
                    $student->fee,
                    $student->plage,
                    $student->minQty,
                    $student->maxQty,
                    $student->location,
                    $student->published_at,
                    $student->expire_at,
                    $student->imageId,
                    $student->classeId,
                    $student->categoryId,
                    $student->tags,
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
            return response()->json(['message' => 'Exporting Failed. ' . $exception->getMessage()], 404);
        }
    }

    public function import(Request $request, Classe $classe)
    {
        if (Auth::user()->id !== $classe->userId && !Auth::user()->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized action.'], 401);
        }
        // Validate the request data
        $validatedData = $request->validate([
            'file' => 'required|file|mimes:csv|max:10240',
        ]);

        try {
            $updated = 0;
            $added = 0;
            $failed = 0;
            // Generate the filename for the exported summary file
            $fileName_summary = 'import_summary_' . $classe->id . '_' . time() . '.csv';
            $csv_summary = Writer::createFromFileObject(new SplTempFileObject());
            // Add headers to the CSV
            $headers = ['old_id', 'new_id', 'status'];
            $csv_summary->insertOne($headers);


            // Store the file locally
            $file = $request->file('file');
            $fileName = 'students-' . Str::uuid() . '.csv';
            $filePath = $file->storeAs('imports', $fileName, 'public');

            // Read the CSV file
            $csv = Reader::createFromPath(storage_path('app/public/' . $filePath), 'r');

            $csv->setHeaderOffset(0);

            // Import the students from the CSV
            foreach ($csv->getRecords() as $record) {
                $itemData = [
                    'id' => $record['id'],
                    'code' => $record['Code'],
                    'title' => $record['Title'],
                    'description' => $record['Description'],
                    'basePrice' => $record['Base_Price'],
                    'price' => $record['Price'],
                    'fee' => $record['Fee'],
                    'plage' => $record['Heures'],
                    'minQty' => $record['Min_Qty'],
                    'maxQty' => $record['Max_Qty'],
                    'location' => $record['Location'],
                    'published_at' => $record['Published_At'],
                    'expire_at' => $record['Expire_At'],
                    'imageId' => $record['Image_ID'],
                    'categoryId' => $record['Category_ID'],
                    'tags' => $record['Tags'],
                    'classeId' => $classe->id,
                ];

                // Create/update Student model and save it to the database
                try {
                    $student = Student::where('classeId', $classe->id)
                        ->findOrFail($itemData['id']);

                    $student->update($itemData);

                    $csv_summary->insertOne([$student->id, $student->id, 'updated']);
                    $updated += 1;
                } catch (Exception $e) {

                    try {
                        $id = $itemData['id'];
                        unset($itemData['id']);
                        $student = Student::create($itemData);
                        $csv_summary->insertOne([$id, $student->id, 'added']);
                        $added += 1;
                    } catch (Exception $e) {
                        $csv_summary->insertOne([$itemData['id'], $student->id, 'failed']);
                        $failed += 1;
                    }
                }
            }
            // Delete the temporary CSV file
            File::delete(storage_path('app/public/' . $filePath));
            // Return a response

            // Set the response headers
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename=' . $fileName_summary,
                'added' => $added,
                'updated' => $updated,
                'failed' => $failed
            ];

            // Return the CSV summary file as a downloadable response
            return Response::make($csv_summary->toString(), 200, $headers);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Import failed.' . $e->getMessage()
            ], 404);
        }
    }
}
