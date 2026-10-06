<?php

namespace App\Http\Controllers\Web;

use App\Models\User;
use App\Models\Attendance;

use App\Models\Seance;
use App\Models\Student;
use App\Models\AttendanceStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\V1\AttendanceController as V1AttendanceController;
use App\Services\MqttNotificationService;
use DateTime;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{

    private static function publishSeanceStartingAlert(Seance $seance): void
    {
        $mqtt = app(MqttNotificationService::class);

        $mqtt->publish('teacher/' . $seance->teacherId . '/seances', [
            'event' => 'seance_starting',
            'seance_id' => $seance->id,
            'salle' => $seance->salle,
            'message' => 'Your class in Salle ' . ($seance->salle ?: 'unknown') . ' is starting.',
        ]);
    }

    public function checklist(Seance $seance)
    {
        if (auth()->user()->id === $seance->teacherId) {

            // 1. Find or create the attendance record for the seance
            $attendance = Attendance::firstOrCreate(['seanceId' => $seance->id]);

            // 2. Get the student IDs already in the attendance list
            // Use pluck() to get a simple array of student IDs
            $absentStudentIds = AttendanceStudent::where('attendanceId', $attendance->id)
                ->pluck('studentId');

            // 3. Get students who are NOT in the attendance list and paginate them
            // Ensure you're working with the relationship query builder, not a collection.
            $students = $seance->classe->students()
                ->whereNotIn('id', $absentStudentIds)
                ->orderBy('num')
                ->paginate(40);
<<<<<<< HEAD
=======

>>>>>>> 7cfde7274412376bfe0117da5f7625213cc6378a

            return view('attendances.checklist', [
                'students' => $students,
                'attendance' => $attendance,
            ]);
        } else {
            return redirect()->route('home');
        }
    }
    
    public function qrcodeCheck(Attendance $attendance)
    {
        return view('attendances.qrcodeCheck', [
            'attendance' => $attendance
        ]);
    }

    public function show(Attendance $attendance)
    {
        if (auth()->user()->id === $attendance->seance->teacherId) {
            return view('attendances.show', [
                'attendance' => $attendance
            ]);
        } else {
            return redirect()->route('home');
        }
    }
    public function savePlage(AttendanceStudent $attendance_student, V1AttendanceController $controller)
    {
        $response = $controller->savePlage($attendance_student);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array

        if ($status_code === 403)
            return redirect()->route('users.login');

        if ($status_code === 200) {
            $message = isset($data['message']) ? $data['message'] : __('vocab.absence_confirmed');
            return back()->with('message', $message);
        } else {
            $message = isset($data['message']) ? $data['message'] : __('vocab.absence_rejected');
            return back()->withErrors(['message' => $message]);;
        }
    }

    public function attendanceToAbsence(Attendance $attendance, V1AttendanceController $controller)
    {
        $seance = $attendance->seance;

        $response = $controller->attendanceToAbsence($attendance);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array
        if ($status_code === 403)
            return redirect()->route('attendances.show', ['attendance' => $attendance]);

        if ($status_code === 200) {
            $message = isset($data['message']) ? $data['message'] : __('vocab.absence_confirmed');
            return redirect()->route('attendances.checklist', ['seance' => $seance])->with('message', $message);
        } else {
            $message = isset($data['message']) ? $data['message'] : __('vocab.absence_rejected');
            return back()->withErrors(['message' => $message]);;
        }
    }


    public function clear(Attendance $attendance, V1AttendanceController $controller)
    {
        $response = $controller->clear($attendance);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array

        if ($status_code === 403)
            return redirect()->route('users.login');

        if ($status_code === 200) {
            $message = isset($data['message']) ? $data['message'] : __('vocab.absence_list_emptied');
            return redirect()->route('seances.index', ['teacherId' => auth()->user()->id])->with('message', $message);
        } else {
            $message = isset($data['message']) ? $data['message'] :  __('vocab.delete_success');
            return back()->withErrors(['message' => $message]);
        }
    }
    public function popStudent(Request $request, Attendance $attendance, V1AttendanceController $controller, $scannedData)
    {
        $response = $controller->popStudent($request, $attendance, $scannedData);
        $status_code = $response->getStatusCode();
        $data = json_decode($response->content(), true);

        if ($status_code === 403) {
            return response()->json([
                'status'  => 'error',
                'message' => __('vocab.not_authorized'),
            ], 403);
        }

        if ($status_code === 200) {
            return response()->json([
                'status'  => 'success',
                'message' => $data['message'] ?? __('vocab.delete_success'),
            ], 200);
        } else if ($status_code === 201) {
            return response()->json([
                'status'  => 'success',
                'message' => $data['message'] ?? __('vocab.delete_success'),
            ], 201);
        }

        return response()->json([
            'status'  => 'error',
            'message' => $data['message'] ?? __('vocab.delete_failed'),
        ], $status_code);
    }



    public function popAttendanceStudent(AttendanceStudent $attendance_student, V1AttendanceController $controller)
    {
        // get seance for redirecting after deletion
        $seance = $attendance_student->attendance->seance;

        $response = $controller->popAttendanceStudent($attendance_student);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array

        if ($status_code === 403)
            return redirect()->route('users.login');

        if ($status_code === 200) {
            $message = isset($data['message']) ? $data['message'] : __('vocab.delete_success');
            return back()->with('message', $message);
        } else if ($status_code === 201) {
            $message = isset($data['message']) ? $data['message'] : __('vocab.delete_success');
            // redirect to seance attendance checklist after deletion
            return redirect()->route('attendances.checklist', ['seance' => $seance])->with('message', $message);
        } else {
            $message = isset($data['message']) ? $data['message'] : __('vocab.delete_failed');
            return back()->withErrors(['message' => $message]);;
        }
    }

    public function pushStudent(Request $request, Attendance $attendance, V1AttendanceController $controller)
    {

        $response = $controller->pushStudent($request, $attendance);

        // Get the HTTP status code from the response
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array

        if ($status_code === 403)
            return redirect()->route('users.login');

        if ($status_code === 200) {
            $message = isset($data['message']) ? $data['message'] : __('vocab.action_success');
            return back()->with('message', $message);
        } else {
            $message = isset($data['message']) ? $data['message'] : __('vocab.action_failed');
            return back()->withErrors(['message' => $message]);;
        }
    }

    public function pushAllStudents(Request $request, Attendance $attendance, V1AttendanceController $controller)
    {

        $response = $controller->pushAllStudents($request, $attendance);

        // Get the HTTP status code from the response
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array

        if ($status_code === 403)
            return redirect()->route('users.login');

        if ($status_code === 200) {
            $message = isset($data['message']) ? $data['message'] : __('vocab.action_success');
            return back()->with('message', $message);
        } else {
            $message = isset($data['message']) ? $data['message'] : __('vocab.action_failed');
            return back()->withErrors(['message' => $message]);;
        }
    }

    public function refreshAttendance(Attendance $attendance, V1AttendanceController $controller)
    {

        $refreshedAttendance = $controller->refreshAttendance($attendance);
        $attendanceStudents = $refreshedAttendance->attendanceStudents;

        $html = view('attendances.attendance_students', [
            'attendance' => $refreshedAttendance
        ])->render();

        return response()->json([
            'html' => $html,
            'count' => $attendanceStudents->count()
        ]);
    }

    /**
     * Manually trigger the periodic tasks.
     * This is intended to be called by an admin from a web route for debugging.
     */
    public function manualPeriodicTasks()
    {
        // Ensure only authorized users (e.g., admins) can run this.
        // You should implement proper authorization logic here.
        // For example: if (auth()->user()->isAdmin()) { ... }

        Log::info("Manual trigger of periodic tasks initiated by user: " . auth()->id());

        $output = [];

        $output[] = "--- Starting Manual Task Execution ---";

        try {
            $startingResults = self::handleStartingSeances();
            $output[] = "Starting Seances Handler Results:";
            $output = array_merge($output, $startingResults);
        } catch (\Exception $e) {
            $errorMessage = "Error handling starting seances: " . $e->getMessage();
            Log::error($errorMessage);
            $output[] = $errorMessage;
        }

        try {
            $endingResults = self::handleEndingSeances();
            $output[] = "Ending Seances Handler Results:";
            $output = array_merge($output, $endingResults);
        } catch (\Exception $e) {
            $errorMessage = "Error handling ending seances: " . $e->getMessage();
            Log::error($errorMessage);
            $output[] = $errorMessage;
        }

        $output[] = "--- Manual Task Execution Finished ---";

        // For a web request, you can display the output to the admin.
        return response('<pre>' . implode("\n", $output) . '</pre>');
    }


    /**
     * This function is designed to be called periodically by Laravel's Task Scheduler.
     * You can place any recurring logic related to attendance here.
     * For example: cleaning up old attendance lists, sending summary reports, etc.
     */
    public static function handlePeriodicTasks()
    {
        $now = now()->setTimezone(config('app.timezone'));
        Log::info("Running periodic tasks at {$now->toDateTimeString()}");
        self::handleStartingSeances($now);
        self::handleEndingSeances($now);
        Log::info("Finished periodic tasks.");
    }

    private static function handleStartingSeances($now = null)
    {

        // 1. Get the current time in the required timezone (Crucial step for issue #2)
        $now = $now ?? now()->setTimezone(config('app.timezone'));
        $controller = app(V1AttendanceController::class);
        $output = [];

        // Extract the day of the week from the current time
        $dayOfWeek = $now->dayOfWeek + 1; // 1  Sunday

        // Define the time window bounds
        $time_start = $now->copy()->subMinutes(2)->format('H:i:s');
        $time_end = $now->copy()->addMinutes(2)->format('H:i:s');

        // --- Handle seances that are starting ---
        // Find seances starting within a 4-minute window (2 mins ago to 2 mins from now)
        $startingSeances = Seance::where('dayId', $dayOfWeek)
            ->where('de','>=', $time_start)
            ->where('de', '<=',$time_end)
            ->get();

        $output[] = "Found " . $startingSeances->count() . " seances starting around {$now->toDateTimeString()}.";
        $startingSeances->each(function ($seance) use ($controller, &$output) {
            // Impersonate the teacher to pass authorization checks in the controller
            $teacher = User::find($seance->teacherId);
            if ($teacher) {
                Auth::login($teacher);

                try {
                    $attendance = Attendance::firstOrCreate(['seanceId' => $seance->id]);

                    // We need a request object for the controller method
                    $request = new Request();

                    // Call the API controller method to fill the attendance list
                    $controller->pushAllStudents($request, $attendance);
<<<<<<< HEAD
=======
                    self::publishSeanceStartingAlert($seance);
>>>>>>> 7cfde7274412376bfe0117da5f7625213cc6378a

                    $logMessage = "Successfully pre-filled attendance for seance ID: {$seance->id}";
                    Log::info($logMessage);
                    $output[] = $logMessage;
                } catch (\Exception $e) {
                    $errorMessage = "Error pre-filling attendance for seance ID: {$seance->id}. Error: " . $e->getMessage();
                    Log::error($errorMessage);
                    $output[] = $errorMessage;
                } finally {
                    Auth::logout(); // Logout after performing the action
                }
            }
        });
        return $output;
    }

    private static function handleEndingSeances($now = null)
    {
        $now = $now ?? now()->setTimezone(config('app.timezone'));
        $controller = app(V1AttendanceController::class);
        $output = [];
        // Extract the day of the week from the current time
        $dayOfWeek = $now->dayOfWeek + 1; // 1 for Sunday

        // Define the time window bounds
        $time_start = $now->copy()->subMinutes(2)->format('H:i:s');
        $time_end = $now->copy()->format('H:i:s');

        // --- Handle seances that have ended ---
        // Find seances that ended in the last 2 minutes
        $endingAttendances = Attendance::query()
            // Assuming 'seance' is the relationship name
            ->whereHas('seance', function ($query) use ($time_start, $time_end, $dayOfWeek) {
                $query->where('dayId', $dayOfWeek)
                    ->where('a','>=', $time_start)
                    ->where('a','<=', $time_end);
            })
            ->with('seance.teacher')
            ->get();

        $output[] = "Found " . $endingAttendances->count() . " seances that ended before {$now->toDateTimeString()}.";

        // Group attendances by teacher to minimize login/logout operations
        $attendancesByTeacher = $endingAttendances->groupBy('seance.teacherId');

        foreach ($attendancesByTeacher as $teacherId => $attendances) {
            $teacher = $attendances->first()->seance->teacher;
            if ($teacher) {
                $logMessage = "Processing " . $attendances->count() . " ending seance(s) for teacher ID: {$teacherId}";
                Log::info($logMessage);
                $output[] = $logMessage;
                Auth::login($teacher);
                try {
                    $attendances->each(function ($attendance) use ($controller) {

                        $controller->attendanceToAbsence($attendance);
                        Log::info("Successfully converted attendance to absence for seance ID: {$attendance->seance->id}");
                    });
                } catch (\Exception $e) {
                    $errorMessage = "Error converting attendance to absence for teacher ID: {$teacherId}. Error: " . $e->getMessage();
                    Log::error($errorMessage);
                    $output[] = $errorMessage;
                } finally {
                    Auth::logout();
                }
            }
        }
        return $output;
    }
}
