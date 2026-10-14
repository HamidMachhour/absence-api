<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Absence;
use App\Models\_Day;
use App\Models\Seance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TerminalController extends Controller
{

    /**
     * Resolve current day IDs from _Day rows by English day name.
     * Falls back to legacy dayOfWeek+1 mapping if _Day rows are missing/misaligned.
     */
    private function currentDayIds(Carbon $now)
    {
        $dayName = strtolower($now->englishDayOfWeek);

        $dayIds = _Day::query()
            ->whereRaw('LOWER(nameEn) = ?', [$dayName])
            ->pluck('id');

        if ($dayIds->isNotEmpty()) {
            return $dayIds;
        }

        return collect([$now->dayOfWeek + 1]);
    }

    /**
     * Show the room selection page.
     */
    public function selectRoom()
    {
        $now = Carbon::now()->setTimezone(config('app.timezone'));
        $dayIds = $this->currentDayIds($now);
        $currentTime = $now->format('H:i:s');

        // Show rooms with a currently active seance to avoid false "no attendance" redirects.
        $rooms = Seance::select('salle')
            ->whereNotNull('salle')
            ->where('salle', '!=', '')
            ->whereIn('dayId', $dayIds)
            ->where('de', '<=', $currentTime)
            ->where('a', '>=', $currentTime)
            ->distinct()
            ->orderBy('salle')
            ->pluck('salle');

        return view('terminal.select-room', ['rooms' => $rooms]);
    }
    /**
     * Display the attendance dashboard for a specific room.
     */
    public function dashboard(Request $request)
    {
        $salle = $request->query('salle');

        if (!$salle) {
            return redirect()->route('terminal.selectRoom')->with('error', __('vocab.please_select_room'));
        }
        $attendanceData = $this->getAttendance($salle);
        if (!$attendanceData) {
            return redirect()->route('terminal.selectRoom')->with('error', __('vocab.no_attendance_data'));
        }

        return view('terminal.dashboard', [
            'salle' => $salle,
            'attendance' => $attendanceData['record'],
            'terminalReadOnly' => $attendanceData['read_only'],
        ]);
    }

    /**
     * API endpoint to get current attendance data for a room.
     * This is called periodically by the dashboard's JavaScript.
     */
    public function getAttendance($salle)
    {
        $now = Carbon::now()->setTimezone(config('app.timezone'));
        $dayIds = $this->currentDayIds($now);
        $currentTime = $now->format('H:i:s');

        // Find the seance currently active in the specified room
        $currentSeance = Seance::where('salle', $salle)
            ->whereIn('dayId', $dayIds)
            ->where('de', '<=', $currentTime)
            ->where('a', '>=', $currentTime)
            ->first();

        if (!$currentSeance) {
            return null;
        }

        // Find the corresponding attendance record for today
        $attendance = Attendance::where('seanceId', $currentSeance->id)
            ->with([
                'attendanceStudents' => function ($query) {
                    $query->with('student.user');
                },
                'seance.classe',
                'seance.subject'
            ])
            ->first();

        if ($attendance) {
            return [
                'record' => $attendance,
                'read_only' => false,
            ];
        }

        // Mobile flow may have finalized this seance into Absence and removed Attendance.
        $absence = Absence::where('seanceId', $currentSeance->id)
            ->with([
                'absenceStudents' => function ($query) {
                    $query->with('student.user');
                },
                'seance.classe',
                'seance.subject'
            ])
            ->latest('created_at')
            ->first();

        if (!$absence) {
            return null;
        }

        // Reuse existing terminal blade by mapping absenceStudents into attendanceStudents.
        $absence->setRelation('attendanceStudents', $absence->absenceStudents);

        return [
            'record' => $absence,
            'read_only' => true,
        ];
    }
    public function findAttendanceBySalle(Request $request)
    {
        $request->validate(['salle' => 'required|numeric']);

        $salle = $request->input('salle');
        $attendance = $this->getAttendance($salle);

        // If no attendance was found, redirect back to room selection with an error
        if (!$attendance) {
            return redirect()->route('terminal.selectRoom')->with('error', __('vocab.no_attendance_data'));
        }

        // Redirect to the dashboard for the selected room
        return redirect()->route('terminal.dashboard', ['salle' => $salle, 'attendance' => $attendance]);
    }

    /**
     * API endpoint to check if current seance is over and get the next attendance.
     * Returns JSON with status, next_attendance_id, and next_exists flag.
     */
    public function checkNextAttendance(Request $request)
    {
        $request->validate(['salle' => 'required|numeric']);
        $salle = $request->input('salle');

        $now = now()->setTimezone(config('app.timezone'));
        $dayIds = $this->currentDayIds($now);
        $currentTime = $now->format('H:i:s');

        // Find the next seance in the specified room after current time
        $nextSeance = Seance::where('salle', $salle)
            ->whereIn('dayId', $dayIds)
            ->where('de', '>', $currentTime)  // Start time must be after current time
            ->orderBy('de')
            ->first();

        if ($nextSeance) {
            // Check if there's an attendance record for this next seance
            $nextAttendance = Attendance::where('seanceId', $nextSeance->id)
                ->first();

            if ($nextAttendance) {
                return response()->json([
                    'next_exists' => true,
                    'next_attendance_id' => $nextAttendance->id,
                    'seance_start_time' => $nextSeance->de,
                    'message' => __('vocab.next_seance_found')
                ]);
            }
        }

        // No next attendance found
        return response()->json([
            'next_exists' => false,
            'message' => __('vocab.no_more_seance')
        ]);
    }
}
