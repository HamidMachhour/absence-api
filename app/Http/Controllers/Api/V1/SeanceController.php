<?php

namespace App\Http\Controllers\Api\V1;

use Exception;
use App\Models\User;
use App\Models\Seance;
use League\Csv\Reader;
use League\Csv\Writer;
use SplTempFileObject;
use App\Models\Student;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class SeanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param int $page_size
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $page_size = 30)
    {
        try {
            if ($request->filled("page_size"))
                $page_size = $request->input("page_size");

            return Seance::filter($request)
                ->orderBy("jourId")
                ->orderBy("de")
                ->latest()
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
    public function show(Request $request, $page_size = 30)
    {
        try {
            if ($request->filled("page_size"))
                $page_size = $request->input("page_size");

            if (auth()->user()->hasRole('teacher'))
                $request['profId'] = auth()->user()->id;

            return $this->index($request);
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
            "profId" => 'required|string',
            "classeId" => 'required|string',
            "matiereId" => 'required|string',
            "jourId" => 'required|integer',
            "de" => 'required|string',
            "a" => 'required|string',
        ]);

        if (!Auth::user()->hasRole('admin') && Auth::user()->id !== $validatedData["profId"]) {
            return response()->json(['message' => 'تم الرفض، لا تمتلك التفويض الكافي للقيام بالعملية'], 403);
        }

        try {
            $de = Carbon::parse($validatedData['de']);
            $a = Carbon::parse($validatedData['a']);
            if ($de->eq($a)) {
                $a = $de->copy();
                $a->addHour();
            } elseif ($a->lt($de)) {
                $t = $a;
                $a = $de;
                $de = $t;
            }

            $validatedData['de'] = $de;
            $validatedData['a'] = $a;

            $exists = $this->seanceOverlap($validatedData);

            if ($exists) {
                return response()->json(['message' => 'لا يمكن اضافة الحصة، يوجد تداخل مع حصة اخرى لنفس القسم'], 405);
            }


            $seance = Seance::create($validatedData);

            // Return a response
            return response()->json(['message' => 'تمت الاضافة بنجاح', 'seance' => $seance], 201);
        } catch (Exception $exception) {
            return response()->json(['message' => 'العملية لم تتم بنجاح، المرجو اعادة المحاولة' . $exception->getMessage()], 404);
        }
    }

    public function seanceOverlap($validatedData)
    {

        $de = $validatedData['de']->setDate(1, 1, 1);
        $a = $validatedData['a']->setDate(1, 1, 1);

        $seances = Seance::where('classeId', $validatedData["classeId"])
            ->where('jourId', $validatedData["jourId"])->get();
        foreach ($seances as $seance) {
            $de1 = Carbon::parse($seance->de)->setDate(1, 1, 1);
            $a1 = Carbon::parse($seance->a)->setDate(1, 1, 1);
            $min = min($de, $de1);
            $max = max($a, $a1);
            if ($max->diffInMinutes($min)  < ($a->diffInMinutes($de) + $a1->diffInMinutes($de1)))
                return true;
        }
        return false;
    }
    /**
     * Update the specified resource in storage.
     *
     */
    public function update(Request $request, Seance $seance)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'غير مسموح، المرجو تسجيل الدخول'], 403);
        }
        if (!Auth::user()->hasRole('admin') && Auth::user()->id !== $seance->profId) {
            return response()->json(['message' => 'تم الرفض، لا تمتلك التفويض الكافي للقيام بالعملية'], 403);
        }

        $validatedData = $request->validate([
            "profId" => 'required|string',
            "classeId" => 'required|string',
            "matiereId" => 'required|string',
            "jourId" => 'required|integer',
            "de" => 'required|string',
            "a" => 'required|string',
        ]);

        try {
            $de = Carbon::parse($validatedData['de']);
            $a = Carbon::parse($validatedData['a']);
            if ($de->eq($a)) {
                $a = $de->copy();
                $a->addHour();
            } elseif ($a->lt($de)) {
                $t = $a;
                $a = $de;
                $de = $t;
            }

            $validatedData['de'] = $de;
            $validatedData['a'] = $a;

            $exists = $this->seanceOverlap($validatedData);

            if ($exists) {
                return response()->json(['message' => 'لا يمكن تعديل الحصة، يوجد تداخل مع حصة اخرى لنفس القسم'], 405);
            }

            // Save the seance
            $seance->update($validatedData);
            // Return the updated seance
            return response()->json(['message' => 'تم التعديل بنجاح', 'seance' => $seance], 200);
        } catch (Exception $exception) {
            return response()->json(['message' => 'لم يتم التعديل بنجاح، المرجو اعادة المحاولة' . $exception->getMessage()], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     */
    public function destroy(Seance $seance)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'غير مسموح، المرجو تسجيل الدخول'], 403);
        }

        if (Auth::user()->id !== $seance->profId && !Auth::user()->hasRole('admin')) {
            return response()->json(['message' => 'تم الرفض، لا تمتلك التفويض الكافي للقيام بالعملية'], 403);
        }
        // Soft delete the store
        $seance->delete();
        // Return a response
        return response()->json(['message' => 'تم الحدف بنجاح'], 200);
    }


    public function export(Seance $seance)
    {
        try {

            if (Auth::user()->id !== $seance->user->id) {
                return response()->json(['message' => 'تم الرفض، لا تمتلك التفويض الكافي للقيام بالعملية'], 403);
            }

            $students = $seance->students->get();


            // Generate the filename for the exported file
            $fileName = 'Store_' . $seance->id . '_students_' . time() . '.csv';


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
                    $student->seanceId,
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

    public function import(Request $request, Seance $seance)
    {
        if (Auth::user()->id !== $seance->userId && !Auth::user()->hasRole('admin')) {
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
            $fileName_summary = 'import_summary_' . $seance->id . '_' . time() . '.csv';
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
                    'seanceId' => $seance->id,
                ];

                // Create/update Student model and save it to the database
                try {
                    $student = Student::where('seanceId', $seance->id)
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
