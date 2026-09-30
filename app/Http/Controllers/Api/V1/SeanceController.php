<?php

namespace App\Http\Controllers\Api\V1;

use Exception;
use App\Models\Seance;
use App\Models\Student;
use League\Csv\Reader;
use League\Csv\Writer;
use SplTempFileObject;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DateInterval;
use DateTime;
use Carbon\Carbon;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class SeanceController extends Controller
{
    private function formatSeanceRange($de, $a): array
    {
        $from = Carbon::parse((string) $de);
        $to = Carbon::parse((string) $a);

        return [
            'from' => $from->format('H:i'),
            'to' => $to->format('H:i'),
            'label' => $from->format('H:i') . ' - ' . $to->format('H:i'),
            'label_short' => $from->format('G') . ' - ' . $to->format('G'),
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @param int $page_size
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $page_size = 30)
    {
        if (!Auth::check()) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }
        try {


            if ($request->filled("page_size"))
                $page_size = $request->input("page_size");

            return Seance::filter($request)
                ->orderBy("dayId")
                ->orderBy("de")
                ->latest()
                ->paginate($page_size);
        } catch (Exception $e) {
            return response()->json(['message' => __('vocab.no_data') . $e->getMessage()], 404);
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
        if (!Auth::check()) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }
        try {

            if ($request->filled("page_size"))
                $page_size = $request->input("page_size");

            if (auth()->user()->hasRole('teacher'))
                $request['teacherId'] = auth()->user()->id;

            return $this->index($request);
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


        if (!Auth::user()->hasRole('admin')) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }

        // Validate the request data
        $validatedData = $request->validate([
            "teacherId" => 'required|string',
            "classeId" => 'required|string',
            "subjectId" => 'required|string',
            "dayId" => 'required|integer',
            "de" => 'required|string',
            "a" => 'required|string',
            "salle" => 'nullable|integer',
        ]);

        try {
            // Create DateTime objects from your input strings
            $de = new DateTime($validatedData['de']);
            $a = new DateTime($validatedData['a']);

            // If 'de' and 'a' are the same, add one hour to 'a'
            if ($de == $a) {
                $a->add(new DateInterval('PT1H'));
            }
            // If 'a' is before 'de', swap them
            elseif ($a < $de) {
                $temp = $a;
                $a = $de;
                $de = $temp;
            }

            // Now you can use the DateTime objects as needed
            // For example, to format them back to strings:
            $validatedData['de'] = $de->format('H:i');
            $validatedData['a'] = $a->format('H:i');

            $exists = $this->seanceOverlap($validatedData);

            if ($exists) {
                return response()->json(['message' => __('vocab.seance_overlap')], 405);
            }


            $seance = Seance::create($validatedData);

            // Return a response
            return response()->json(['message' => __('vocab.action_success'), 'seance' => $seance], 201);
        } catch (Exception $exception) {
            return response()->json(['message' => __('vocab.task_failed') . ' ' . __('vocab.try_again') . $exception->getMessage()], 404);
        }
    }
    /**
     * Check if a seance overlaps with existing seances for the same class and day
     * @param array $validatedData Contains 'de'(start), 'a'(end), 'classeId', 'dayId'
     * @return bool True if overlap exists, false otherwise
     */

    // In V1SeanceController
    public function seanceOverlap($validatedData): bool
    {
        $query = Seance::where('classeId', $validatedData["classeId"])
            ->where('dayId', $validatedData["dayId"]);

        // This is the crucial part that handles all overlap cases
        $query->where('de', '<', $validatedData['a'])
            ->where('a', '>', $validatedData['de']);

        // Exclude the current seance from the overlap check (essential for updates)
        if (isset($validatedData['id'])) {
            $query->where('id', '!=', $validatedData['id']);
        }

        return $query->exists();
    }
    /**
     * Update the specified resource in storage.
     *
     */
    public function update(Request $request, Seance $seance)
    {
        if (!Auth::check()) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }
        if (!Auth::user()->hasRole('admin')) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }

        $validatedData = $request->validate([
            "teacherId" => 'required|string',
            "classeId" => 'required|string',
            "subjectId" => 'required|string',
            "dayId" => 'required|integer',
            "de" => 'required|string',
            "a" => 'required|string',
            "salle" => 'nullable|numeric',
        ]);

        $validatedData['id'] = $seance->id;

        try {
            // Create DateTime objects from your input strings
            $de = new DateTime($validatedData['de']);
            $a = new DateTime($validatedData['a']);

            // If 'de' and 'a' are the same, add one hour to 'a'
            if ($de == $a) {
                $a->add(new DateInterval('PT1H'));
            }
            // If 'a' is before 'de', swap them
            elseif ($a < $de) {
                $temp = $a;
                $a = $de;
                $de = $temp;
            }

            // Now you can use the DateTime objects as needed
            // For example, to format them back to strings:
            $validatedData['de'] = $de->format('H:i');
            $validatedData['a'] = $a->format('H:i');

            $exists = $this->seanceOverlap($validatedData);

            if ($exists) {
                return response()->json(['message' => __('vocab.seance_overlap')], 405);
            }

            // Save the seance
            $seance->update($validatedData);
            // Return the updated seance
            return response()->json(['message' => __('vocab.update_success'), 'seance' => $seance], 200);
        } catch (Exception $exception) {
            return response()->json(['message' => __('vocab.update_failure') . ' ' . __('vocab.try_again') . $exception->getMessage()], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     */
    public function destroy(Seance $seance)
    {
        if (!Auth::check()) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }

        if (!Auth::user()->hasRole('admin')) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }
        // Soft delete the seance
        $seance->delete();
        // Return a response
        return response()->json(['message' => __('vocab.delete_success')], 200);
    }


    /**
     * Soft deletes all Seance records.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyAll(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }

        if (!Auth::user()->hasRole('admin')) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }

        // Soft delete all seances by updating the deleted_at timestamp
        Seance::filter($request)->get()->each->delete();

        // Return a response
        return response()->json(['message' => __('vocab.delete_success')], 200);
    }

    //function to export seances to excel or csv file
    public function export(Request $request)
    {

        // ckeck if the user is admin or manager
        if (!Auth::user()->hasRole('admin') && !Auth::user()->hasRole('manager')) {

            return response()->json(['message' => __('vocab.not_authorized')], 404);
        }
        try {

            $fileName = 'seances_export_' . date('Y_m_d_H_i_s') . '.csv';
            // get seances according to filters
            $seances = Seance::filter($request)
                ->latest()
                ->get();
            // create csv file
            $csv = Writer::createFromFileObject(new SplTempFileObject());


            // add header to csv file
            $headers = ['id', 'subjectId', 'teacherId', 'classeId', 'dayId', 'salle', 'de', 'a'];
            $csv->insertOne($headers);
            // add users to csv file
            foreach ($seances as $seance) {
                $itemData = [
                    $seance->id,
                    $seance->subjectId,
                    $seance->teacherId,
                    $seance->classeId,
                    $seance->dayId,
                    $seance->salle,
                    $seance->de,
                    $seance->a
                ];
                $csv->insertOne($itemData);
            }
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename=' . $fileName,
            ];

            return Response::make($csv->toString(), 200, $headers);
        } catch (Exception $e) {
            return response()->json(['message' => __('vocab.no_data') . $e->getMessage()], 404);
        }
    }

    public function import(Request $request)
    {
        if (!Auth::user()->hasRole('admin')) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }

        $request->validate([
            'file' => 'required|file|mimes:csv|max:10240',
        ]);

        try {
            $added = 0;
            $failed = 0;

            $fileName_summary = 'import_seances_summary_' . time() . '.csv';
            $csv_summary = Writer::createFromFileObject(new SplTempFileObject());
            $headers = ['info', 'details', 'status'];
            $csv_summary->insertOne($headers);

            $file = $request->file('file');
            $fileName = 'seances-' . Str::uuid() . '.csv';
            $filePath = $file->storeAs('imports', $fileName, 'public');

            $csv = Reader::createFromPath(storage_path('app/public/' . $filePath), 'r');
            $csv->setHeaderOffset(0);

            foreach ($csv->getRecords() as $record) {
                try {
                    // Create DateTime objects from your input strings
                    $de = new DateTime($record['de']);
                    $a = new DateTime($record['a']);

                    // If 'de' and 'a' are the same, add one hour to 'a'
                    if ($de == $a) {
                        $a->add(new DateInterval('PT1H'));
                    }
                    // If 'a' is before 'de', swap them
                    elseif ($a < $de) {
                        $temp = $a;
                        $a = $de;
                        $de = $temp;
                    }

                    // Now you can use the DateTime objects as needed
                    // For example, to format them back to strings:
                    $record['de'] = $de->format('H:i');
                    $record['a'] = $a->format('H:i');

                    $exists = $this->seanceOverlap($record);

                    if ($exists) {
                        $csv_summary->insertOne([($record['id'] ?? 'error'), implode(', ', $record), 'overlapped']);
                        $failed++;
                    } else {
                        unset($record['id']);
                        $seance = Seance::create($record);
                        $csv_summary->insertOne([($record['id'] ?? 'new'), $seance->id, 'added']);
                        $added++;
                    }
                } catch (Exception $e) {

                    $csv_summary->insertOne([($record['id'] ?? 'error'), implode(', ', $record), 'failed']);
                    $failed++;
                }
            }

            File::delete(storage_path('app/public/' . $filePath));

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename=' . $fileName_summary,
                'added' => $added,
                'failed' => $failed
            ];

            return Response::make($csv_summary->toString(), 200, $headers);
        } catch (Exception $e) {
            return response()->json([
                'message' => __('vocab.action_failure') . $e->getMessage()
            ], 404);
        }
    }

    public function roster(string $seanceId)
    {
        if (!Auth::check()) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }

        $seance = Seance::with(['classe', 'subject'])->find($seanceId);

        if (!$seance) {
            return response()->json(['message' => __('vocab.no_data')], 404);
        }

        if (Auth::id() !== $seance->teacherId) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }

        $students = Student::where('classeId', $seance->classeId)
            ->with(['user.userImage'])
            ->orderBy('num')
            ->get()
            ->map(function (Student $student) {
                $user = $student->user;

                return [
                    'id' => $student->id,
                    'num' => $student->num,
                    'name' => trim(($user?->firstNameEn ?? $user?->firstName ?? '') . ' ' . ($user?->lastNameEn ?? $user?->lastName ?? '')),
                    'photo_url' => $user?->userImage?->uri,
                ];
            })
            ->values();

        return response()->json([
            'seance_id' => $seance->id,
            'classe_name' => $seance->classe?->nameEn ?? $seance->classe?->name,
            'subject' => $seance->subject?->nameEn ?? $seance->subject?->name,
            'salle' => $seance->salle,
            'students' => $students,
        ], 200);
    }

    public function mySeances(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }

        $seances = Seance::where('teacherId', Auth::id())
            ->with(['subject', 'classe', 'day'])
            ->orderBy('dayId')
            ->orderBy('de')
            ->get()
            ->map(function (Seance $seance) {
                $range = $this->formatSeanceRange($seance->de, $seance->a);

                return [
                    'id' => $seance->id,
                    'day_id' => $seance->dayId,
                    'day_name' => $seance->day?->nameEn ?? $seance->day?->name,
                    'salle' => $seance->salle,
                    'subject' => $seance->subject?->nameEn ?? $seance->subject?->name,
                    'classe_name' => $seance->classe?->nameEn ?? $seance->classe?->name,
                    'de' => $range['from'],
                    'a' => $range['to'],
                    'time_range' => $range['label'],
                    'time_range_short' => $range['label_short'],
                ];
            })
            ->values();

        return response()->json([
            'teacher_id' => Auth::id(),
            'seances' => $seances,
        ], 200);
    }
}
