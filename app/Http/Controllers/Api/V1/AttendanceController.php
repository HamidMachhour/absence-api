<?php

namespace App\Http\Controllers\Api\V1;

use Exception;
use App\Models\Attendance;
use App\Models\Absence;
use App\Models\Seance;
use App\Models\Student;
use App\Models\AttendanceStudent;
use Illuminate\Http\Request;
use App\Models\AbsenceStudent;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\MqttNotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AttendanceController extends Controller
{

    protected MqttNotificationService $mqttNotificationService;

    public function __construct(MqttNotificationService $mqttNotificationService)
    {
        $this->mqttNotificationService = $mqttNotificationService;
    }

    protected function shouldConvertToAbsenceNow(Seance $seance, ?string $timestamp = null): bool
    {
        $reference = $timestamp ? Carbon::parse($timestamp) : now();

        return $reference->format('H:i:s') >= Carbon::parse($seance->a)->format('H:i:s');
    }

    protected function publishAttendanceUpdatedEvent(Seance $seance, array $absentStudentIds): void
    {
        $topic = 'attendance/updated/' . ($seance->salle ?: 'unknown');

        $this->mqttNotificationService->publish($topic, [
            'event' => 'attendance_updated',
            'seance_id' => $seance->id,
            'salle' => $seance->salle,
            'absent_student_ids' => array_values($absentStudentIds),
            'updated_at' => now()->toDateTimeString(),
        ]);
    }

    public function submitForSeance(Request $request, Seance $seance)
    {
        if (!Auth::check() || Auth::id() !== $seance->teacherId) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }

        $validatedData = $request->validate([
            'absent_student_ids' => ['required', 'array'],
            'absent_student_ids.*' => ['string'],
            'timestamp' => ['nullable', 'date'],
            'finalize' => ['nullable', 'boolean'],
        ]);

        // Mobile submission is treated as final by default.
        $finalize = array_key_exists('finalize', $validatedData)
            ? (bool) $validatedData['finalize']
            : true;

        $classStudentIds = Student::where('classeId', $seance->classeId)->pluck('id')->all();
        $classStudentIdMap = array_fill_keys($classStudentIds, true);

        $absentStudentIds = array_values(array_filter(
            array_unique($validatedData['absent_student_ids']),
            fn($studentId) => isset($classStudentIdMap[$studentId])
        ));

        try {
            $attendance = DB::transaction(function () use ($seance, $absentStudentIds) {
                $attendance = Attendance::firstOrCreate(['seanceId' => $seance->id]);
                $attendance->attendanceStudents()->delete();

                if (!empty($absentStudentIds)) {
                    $plage = $seance->plageInMinutes();
                    $now = now();

                    $rows = array_map(function ($studentId) use ($attendance, $plage, $now) {
                        return [
                            'id' => (string) Str::uuid(),
                            'attendanceId' => $attendance->id,
                            'studentId' => $studentId,
                            'plage' => $plage,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }, $absentStudentIds);

                    AttendanceStudent::insert($rows);
                }

                return $attendance;
            });

            $attendance->refresh();
            $absenceCreated = false;

            if ($finalize || $this->shouldConvertToAbsenceNow($seance, $validatedData['timestamp'] ?? null)) {
                $conversionResponse = $this->attendanceToAbsence($attendance);

                if ($conversionResponse->getStatusCode() !== 200) {
                    return $conversionResponse;
                }

                $absenceCreated = true;
            }

            $this->publishAttendanceUpdatedEvent($seance, $absentStudentIds);

            return response()->json([
                'message' => __('vocab.action_success'),
                'seance_id' => $seance->id,
                'attendance_id' => $attendance->id,
                'absent_student_ids' => $absentStudentIds,
                'absence_created' => $absenceCreated,
            ], 200);
        } catch (Exception $exception) {
            Log::error('Failed to submit seance attendance: ' . $exception->getMessage());

            return response()->json([
                'message' => __('vocab.action_failure') . ' ' . __('vocab.try_again'),
            ], 500);
        }
    }

    public function clear(Attendance $attendance)
    {

        if (!Auth::check()) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }

        if (auth()->user()->id !== $attendance->seance->teacherId) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }
        // to allow generate new id for attendance
        $attendance->delete();

        return response()->json(['message' => __('vocab.delete_success')], 200);
    }

    public function attendanceToAbsence(Attendance $attendance)
    {
        // 1. Authorization Check (No change needed - this is correct)
        if (!Auth::check() || auth()->user()->id !== $attendance->seance->teacherId) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }

        // Get the related students *once*
        $attendanceStudents = $attendance->attendanceStudents;

        // 2. NEW: Check if there are students to process (AttendanceStudents > 0)
        if ($attendanceStudents->isEmpty()) {
            // Logically, if the attendance is empty, there's nothing to convert to absence.
            // We can still clean up the parent Attendance record if desired, but 
            // the core transaction for moving data should be skipped.

            // OPTION A: Simply delete the empty Attendance record and return success.
            $attendance->delete();
            return response()->json(['message' => __('vocab.action_success') . ' (Empty attendance record deleted)'], 200);

            // OPTION B: Return success without deleting, assuming the calling job handles clean up.
            // return response()->json(['message' => __('vocab.action_success') . ' (No students to process)'], 200);
        }

        try {
            DB::transaction(function () use ($attendance, $attendanceStudents) {

                // 1. Create the Absence record (New parent record)
                $absence = Absence::create([
                    'seanceId' => $attendance->seanceId,
                    'created_at' => $attendance->created_at
                ]);

                // 2. Prepare bulk insert data for AbsenceStudent (Mapping from AttendanceStudent)
                $bulkInsert = $attendanceStudents->map(function ($a) use ($absence) {
                    return [
                        'id' => Str::uuid(),
                        'studentId' => $a->studentId,
                        'absenceId' => $absence->id,
                        'plage' => $a->plage,
                        'justification' => $a->justification,
                        'created_at' => $a->created_at,
                        'updated_at' => now(),
                    ];
                })->toArray();

                // 3. Bulk insert into absence_students
                AbsenceStudent::insert($bulkInsert);

                // 4. Delete all attendanceStudents in a single query (Cleanup child records)
                // Use the loaded collection to avoid a second query if possible, 
                // but deleting via the relationship is cleaner.
                $attendance->attendanceStudents()->delete();

                // 5. Delete the attendance record itself (Cleanup parent record)
                $attendance->delete();
            });

            return response()->json(['message' => __('vocab.action_success')], 200);
        } catch (Exception $e) {
            // Log the error for debugging
            Log::error("Failed to convert Attendance to Absence: {$e->getMessage()}");
            return response()->json(['message' => __('vocab.action_failed') . ': ' . $e->getMessage()], 403);
        }
    }

    public function popStudent(Request $request, Attendance $attendance, $scannedData)
    {
        // 1. Authorization check: Combined for clarity and efficiency.
        /*
        if (!Auth::check() || auth()->user()->id !== $attendance->seance->teacherId) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }*/

        // 2. Decode the QR code data.
        $decodedData = json_decode($scannedData, true);

        // 3. Data validation: Check for valid JSON and required keys.
        if ($decodedData === null || !isset($decodedData['tag_id'])) {
            return response()->json(['message' => __('vocab.invalid_qr_code_data')], 400);
        }

        // 4. Extract data from the decoded payload.
        $tagId = $decodedData['tag_id'];

        try {
            // 5. Find the specific student using the scanned data for validation.
            // The whereHas() is crucial for ensuring the student's tag matches.
            $studentIds = Student::whereHas('user', function ($query) use ($tagId) {
                $query->where('tag_id', $tagId);
            })->pluck('id');

            // 6. Delete the specific attendance record in one query.
            $deletedCount = AttendanceStudent::where('attendanceId', $attendance->id)
                ->whereIn('studentId', $studentIds)
                ->delete();

            // 7. Check if a record was actually deleted.
            if ($deletedCount > 0) {
                // if attendance list is emptied after deletion, delete attendance record
                if ($attendance->attendanceStudents()->count() === 0) {
                    $attendance->delete();
                    return response()->json(['message' => __('vocab.delete_success')], 201);
                }
                return response()->json(['message' => __('vocab.delete_success')], 200);
            } else {
                // A record was not found to be deleted.
                return response()->json(['message' => __('vocab.delete_failed')], 404);
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // This is a more precise way to handle cases where the student is not found
            // or the tag doesn't match the student.
            return response()->json(['message' => __('vocab.delete_failed')], 404);
        } catch (\Exception $e) {
            Log::error('An unexpected error occurred: ' . $e->getMessage());
            return response()->json(['message' => __('vocab.delete_failed')], 500);
        }
    }

    public function popAttendanceStudent(AttendanceStudent $attendance_student)
    {

        if (!Auth::check()) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }

        if (auth()->user()->id !== $attendance_student->attendance->seance->teacherId) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }

        if ($attendance_student->delete()) {
            // if attendance list is emptied after deletion, delete attendance record
            if ($attendance_student->attendance->attendanceStudents()->count() === 0) {
                $attendance_student->attendance->delete();
                return response()->json(['message' => __('vocab.delete_success')], 201);
            }
            return response()->json(['message' => __('vocab.delete_success')], 200);
        }

        return response()->json(['message' => __('vocab.delete_failed')], 404);
    }

    public function pushStudent(Request $request, Attendance $attendance)
    {
        // 1. Authorization check
        if (!Auth::check()) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }

        try {
            // 2. Validate the request data
            $validatedData = $request->validate([
                'studentId' => 'required|string',
            ]);

            $user = Auth::user();
            $seance = $attendance->seance;
            // 3. Authorization and data consistency checks
            if ($user->id !== $seance->teacherId) {
                return response()->json(['message' => __('vocab.not_authorized')], 403);
            }

            $student = Student::findOrFail($validatedData['studentId']);

            if ($student->classeId !== $seance->classeId) {
                return response()->json(['message' => __('vocab.student_class_mismatch')], 400);
            }

            // 5. Determine 'plage' 
            $plage = $request->input('plage', $seance->plageInMinutes());

            // 6. Push the student to the attendance record
            if ($attendance->pushStudent($student->id, $plage)) {
                return response()->json(['message' => __('vocab.action_success')], 200);
            }

            // 7. Fallback failure response
            return response()->json(['message' => __('vocab.action_failure')], 404);
        } catch (Exception $exception) {
            return response()->json(['message' => __('vocab.action_failure') . ': ' . $exception->getMessage()], 500);
        }
    }

    public function pushAllStudents(Request $request, Attendance $attendance)
    {

        if (!Auth::check()) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }
        try {
            $user = Auth::user();
            $seance = $attendance->seance;

            // 3. Authorization and data consistency checks
            if ($user->id !== $seance->teacherId) {
                return response()->json(['message' => __('vocab.not_authorized')], 403);
            }
            $plage = $request->filled('plage') ?
                $request->input('plage') : $seance->plageInMinutes();

            if ($attendance->pushAllStudents($plage))
                return response()->json(['message' => __('vocab.action_success')], 200);

            return response()->json(['message' => __('vocab.action_failure')], 404);
        } catch (Exception $exception) {
            return response()->json(['message' => __('vocab.action_failure') . ' ' . $exception->getMessage()], 404);
        }
    }

    public function savePlage(AttendanceStudent $attendance_student)
    {

        if (!Auth::check()) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }
        if (auth()->user()->id !== $attendance_student->attendance->seance->teacherId) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }
        try {
            // Validate the request data
            $validatedData = request()->validate([
                'plage' => 'required|numeric'
            ]);

            $seancePlage = $attendance_student->attendance->seance->plageInMinutes();

            $plage = max(0, min($validatedData['plage'], $seancePlage));

            $attendance_student->update(['plage' => $plage]);
            return response()->json(['message' => __('vocab.update_success')], 200);


            return response()->json(['message' => __('vocab.update_failure') . ' ' . __('vocab.try_again')], 404);
        } catch (Exception $exception) {
            return response()->json(['message' => __('vocab.update_failure') . ' ' . __('vocab.try_again') . $exception->getMessage()], 404);
        }
    }

    // In your AttendanceController.php

    public function refreshAttendance(Attendance $attendance)
    {

        // If the attendance has no students, ensure an attendance record exists for the seance.
        // This will create one if it was deleted, or just return the existing empty one.
        if ($attendance->attendanceStudents()->doesntExist()) {
            return Attendance::firstOrCreate(['seanceId' => $attendance->seanceId]);
        }

        // Otherwise, return the attendance with a fresh list of students.
        return $attendance->fresh('attendanceStudents');
    }
}
