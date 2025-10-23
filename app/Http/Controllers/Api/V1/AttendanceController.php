<?php

namespace App\Http\Controllers\Api\V1;

use Exception;
use App\Models\Attendance;
use App\Models\Absence;
use App\Models\Student;
use App\Models\AttendanceStudent;
use Illuminate\Http\Request;
use App\Models\AbsenceStudent;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AttendanceController extends Controller
{

    public function clear(Attendance $attendance)
    {

        if (!Auth::check()) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }

        if (auth()->user()->id !== $attendance->seance->teacherId) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }
        DB::transaction(function () use ($attendance) {
            $attendance->attendanceStudents()->delete();
            // to allow generate new id for attendance
            $attendance->delete();
        });

        return response()->json(['message' => __('vocab.delete_success')], 200);
    }

    public function attendanceToAbsence(Attendance $attendance)
    {
        if (!Auth::check() || auth()->user()->id !== $attendance->seance->teacherId) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }

        try {
            DB::transaction(function () use ($attendance) {

                // 1. Create the Absence record
                $absence = Absence::create([
                    'seanceId' => $attendance->seanceId,
                    'created_at' => $attendance->created_at
                ]);

                // 2. Prepare bulk insert data for AbsenceStudent
                $bulkInsert = $attendance->attendanceStudents->map(function ($a) use ($absence) {
                    return [
                        'id' => Str::uuid(),
                        'studentId' => $a->studentId,
                        'absenceId' => $absence->id,
                        'plage' => $a->plage,
                        'justification' => $a->justification,
                        'created_at' => $a->created_at,
                        'updated_at' => now(), // required if your table uses timestamps
                    ];
                })->toArray();

                // 3. Bulk insert into absence_students
                AbsenceStudent::insert($bulkInsert);

                // 4. Delete all attendanceStudents in a single query
                $attendance->attendanceStudents()->delete();

                // 5. Delete the attendance record itself
                $attendance->delete();
            });

            return response()->json(['message' => __('vocab.action_success')], 200);
        } catch (Exception $e) {
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

        if ($attendance_student->delete())
            return response()->json(['message' => __('vocab.delete_success')], 200);

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


        //return Attendance::where('id',$attendance->id)->first(); //or
        return $attendance->fresh('attendanceStudents'); // reload relation


    }
}
