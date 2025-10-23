<?php

namespace App\Http\Controllers\Web;

use App\Models\Attendance;


use App\Models\Seance;
use App\Models\Student;
use App\Models\AttendanceStudent;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\V1\AttendanceController as V1AttendanceController;
use DateTime;

class AttendanceController extends Controller
{

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
            $students = $seance->classe->students()->whereNotIn('id', $absentStudentIds)->paginate(40);

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
        $seance =$attendance->seance;

        $response = $controller->attendanceToAbsence($attendance);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array
        if ($status_code === 403)
            return redirect()->route('attendances.show', ['attendance' => $attendance]);

        if ($status_code === 200) {
            $message = isset($data['message']) ? $data['message'] : __('vocab.absence_confirmed');
            return redirect()->route('attendances.checklist',['seance'=>$seance])->with('message', $message);
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
        }

        return response()->json([
            'status'  => 'error',
            'message' => $data['message'] ?? __('vocab.delete_failed'),
        ], $status_code);
    }



    public function popAttendanceStudent(AttendanceStudent $attendance_student, V1AttendanceController $controller)
    {
        $response = $controller->popAttendanceStudent($attendance_student);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array

        if ($status_code === 403)
            return redirect()->route('users.login');

        if ($status_code === 200) {
            $message = isset($data['message']) ? $data['message'] : __('vocab.delete_success');
            return back()->with('message', $message);
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
        $attendance = $controller->refreshAttendance($attendance);

        $html = view('attendances.attendance_students', compact('attendance'))->render();
        $count = $attendance->attendanceStudents->count();

        return response()->json([
            'html' => $html,
            'count' => $count
        ]);
    }
}
