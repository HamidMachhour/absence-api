<?php

namespace App\Http\Controllers\Api\V1;

use Exception;
use App\Models\User;
use App\Models\Absence;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\AbsenceStudent;
use App\Models\MisconductReport;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AbsenceController extends Controller
{

    public function ofUser(Request $request, User $user, $page_size = 40)
    {
        if (!Auth::check() || Auth::user()->id != $user->id) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }

        try {
            $page_size = $request->input("page_size", 40);

            // 1. Get the IDs of all Student records associated with the User
            $studentIds = Student::where('userId', $user->id)->pluck('id');

            // 2. Use a 'whereHas' relationship check (or a similar join) 
            // to filter Absences based on the student IDs.
            $absences = Absence::whereHas('absenceStudents', function ($query) use ($studentIds) {
                $query->whereIn('studentId', $studentIds);
            })
                // Note: You must ensure an 'students' relationship exists on your Absence model, 
                // which typically relates through AbsenceStudent.

                // Assuming your existing filter and eager loading are correct:
                ->filter($request)
                ->with('seance')
                ->paginate($page_size);

            return $absences;
        } catch (Exception $e) {
            return response()->json(['message' => __('vocab.no_data')], 404);
        }
    }

    public function index(Request $request, $page_size = 40)
    {
        if (!Auth::check()) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }

        if (!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('manager')) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }

        try {
            if ($request->filled("page_size"))
                $page_size = $request->input("page_size");

            return Absence::latest()
                ->filter($request)
                ->with('seance')
                ->paginate($page_size);
        } catch (Exception $e) {
            return response()->json(['message' => __('vocab.no_data') . $e->getMessage()], 404);
        }
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Absence  $absence
     * @return \Illuminate\Http\Response
     */
    public function destroy(Absence $absence)
    {
        if (!Auth::check()) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }

        if (!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('manager')) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }
        // if the absence is related to a class that is owned by a manager, check if the user is the owner of the class
        if (auth()->user()->hasRole('manager') && !auth()->user()->hasRole('admin')) {
            $user = $absence->seance->classe->user;
            if ($user->id != auth()->user()->id) {
                return response()->json(['message' => __('vocab.not_authorized')], 403);
            }
        }

        try {
            DB::transaction(function () use ($absence) {
                //$absence->absenceStudents()->delete();
                $absence->delete();
            });
            // Return a response
            return response()->json(['message' => __('vocab.delete_success')], 200);
        } catch (Exception $e) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }
    }


    public function popStudent(Absence $absence, AbsenceStudent $absence_student)
    {

        if (!Auth::check()) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }

        if (!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('manager')) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }

        // if the absence is related to a class that is owned by a manager, check if the user is the owner of the class
        if (auth()->user()->hasRole('manager') && !auth()->user()->hasRole('admin')) {
            $user = $absence->seance->classe->user;
            if ($user->id != auth()->user()->id) {
                return response()->json(['message' => __('vocab.not_authorized')], 403);
            }
        }

        if ($absence_student->absenceId === $absence->id) {
            $absence_student->delete();
            return response()->json(['message' => __('vocab.delete_success')], 200);
        }

        return response()->json(['message' => __('vocab.delete_failed')], 404);
    }


    public function saveJustification(Absence $absence, AbsenceStudent $absence_student)
    {

        if (!Auth::check()) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }
        if (!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('manager')) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }
        // if the absence is related to a class that is owned by a manager, check if the user is the owner of the class
        if (auth()->user()->hasRole('manager') && !auth()->user()->hasRole('admin')) {
            $user = $absence->seance->classe->user;
            if ($user->id != auth()->user()->id) {
                return response()->json(['message' => __('vocab.not_authorized')], 403);
            }
        }

        try {
            // Validate the request data
            $validatedData = request()->validate([
                'justification' => 'nullable|string'
            ]);


            $justification = $validatedData['justification'];
            if ($absence_student->absenceId === $absence->id) {
                $absence_student->update(['justification' => $justification]);
                return response()->json(['message' => __('vocab.update_success')], 200);
            }

            return response()->json(['message' => __('vocab.update_failure') . ' ' . __('vocab.try_again')], 404);
        } catch (Exception $exception) {
            return response()->json(['message' => __('vocab.update_failure') . ' ' . __('vocab.try_again') . $exception->getMessage()], 404);
        }
    }

    public function getChartAbsenceData(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }

        $user = Auth::user();

        // Determine the correct date formatting function based on the database driver
        $connection = DB::connection()->getDriverName();
        $dateFormat = 'strftime("%Y-%m-%d", AbsenceStudent.created_at)'; // Default for SQLite
        if ($connection === 'mysql') {
            $dateFormat = "DATE_FORMAT(AbsenceStudent.created_at, '%Y-%m-%d')";
        }

        $absenceQuery = AbsenceStudent::query()
            ->select(
                DB::raw($dateFormat . ' as date'),
                DB::raw('Student.id as student_id'),
                DB::raw('_User.firstName'),
                DB::raw('_User.lastName'),
                DB::raw('Student.classeId as classeId'),
                DB::raw('Classe.name as classe_name'),
                DB::raw('count(AbsenceStudent.id) as nb_absences'),
                DB::raw('sum(AbsenceStudent.plage) as sum_plages'),
                DB::raw('count(AbsenceStudent.justification) as justifie'),
                DB::raw('count(AbsenceStudent.id) - count(AbsenceStudent.justification) as non_justifie')
            )
            ->join('Student', 'AbsenceStudent.studentId', '=', 'Student.id')
            ->join('Classe', 'Classe.id', '=', 'Student.classeId')
            ->join('_User', '_User.id', '=', 'Student.userId')
            ->whereNull('AbsenceStudent.deleted_at')
            ->whereNull('_User.deleted_at')
            ->whereNull('Student.deleted_at')
            ->whereNull('Classe.deleted_at');

        // 🔥 NEW: Limit data to the student's classes if the user is a student
        if ($user->hasRole('student')) {
            // Get all classeIds associated with the connected student
            $classeIds = Student::where('userId', $user->id)->pluck('classeId');

            // Apply the filter
            $absenceQuery->whereIn('Student.classeId', $classeIds);

            // Also, restrict to this specific student's ID for absence data
            $studentIds = Student::where('userId', $user->id)->pluck('id');
            $absenceQuery->whereIn('Student.id', $studentIds);
        }


        // Apply other filters from the request
        $absenceData = $absenceQuery
            ->when($request->from, function ($query) use ($request) {
                return $query->whereDate('AbsenceStudent.created_at', '>=', $request->from);
            })
            ->when($request->to, function ($query) use ($request) {
                return $query->whereDate('AbsenceStudent.created_at', '<=', $request->to);
            })
            ->when($request->student && !$user->hasRole('student'), function ($query) use ($request) {
                // Allow filtering by student ONLY if the user is NOT a student
                return $query->where('Student.id', $request->student);
            })
            ->when($request->classe && !$user->hasRole('student'), function ($query) use ($request) {
                // Allow filtering by class ONLY if the user is NOT a student
                return $query->where('Student.classeId', $request->classe);
            })
            ->groupBy(
                DB::raw($dateFormat),
                'Student.id',
                '_User.firstName',
                '_User.lastName',
                'Classe.name',
                'Student.classeId'
            )
            ->limit(50)
            ->get();

        return response()->json(['absenceData' => $absenceData]);
    }


    public function getChartMisconductReportData(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }

        $user = Auth::user();

        // Determine the correct date formatting function based on the database driver
        $connection = DB::connection()->getDriverName();
        $dateFormat = 'strftime("%Y-%m-%d", MisconductReport.created_at)'; // Default for SQLite
        if ($connection === 'mysql') {
            $dateFormat = "DATE_FORMAT(MisconductReport.created_at, '%Y-%m-%d')";
        }

        $reportQuery = MisconductReport::query()
            ->select(
                DB::raw($dateFormat . ' as date'),
                DB::raw('Student.id as student_id'),
                DB::raw('_User.firstName'),
                DB::raw('_User.lastName'),
                DB::raw('Student.classeId as classeId'),
                DB::raw('Classe.name as classe_name'),
                DB::raw('MisconductReport.status'),
                DB::raw('count(MisconductReport.id) as nb_reports')
            )
            ->join('Student', 'MisconductReport.studentId', '=', 'Student.id')
            ->join('Classe', 'Classe.id', '=', 'Student.classeId')
            ->join('_User', '_User.id', '=', 'Student.userId')
            ->whereNull('MisconductReport.deleted_at')
            ->whereNull('_User.deleted_at')
            ->whereNull('Student.deleted_at')
            ->whereNull('Classe.deleted_at');


        // 🔥 NEW: Limit data to the student's classes and student ID if the user is a student
        if ($user->hasRole('student')) {
            // Get all classeIds associated with the connected student
            $classeIds = Student::where('userId', $user->id)->pluck('classeId');

            // Apply the filter
            $reportQuery->whereIn('Student.classeId', $classeIds);

            // Also, restrict to this specific student's ID for misconduct reports
            $studentId = Student::where('userId', $user->id)->pluck('id');
            $reportQuery->whereIn('Student.id', $studentId);
        }


        // Apply other filters from the request
        $reportData = $reportQuery
            ->when($request->from, function ($query) use ($request) {
                return $query->whereDate('MisconductReport.created_at', '>=', $request->from);
            })
            ->when($request->to, function ($query) use ($request) {
                return $query->whereDate('MisconductReport.created_at', '<=', $request->to);
            })
            ->when($request->student && !$user->hasRole('student'), function ($query) use ($request) {
                // Allow filtering by student ONLY if the user is NOT a student
                return $query->where('Student.id', $request->student);
            })
            ->when($request->classe && !$user->hasRole('student'), function ($query) use ($request) {
                // Allow filtering by class ONLY if the user is NOT a student
                return $query->where('Student.classeId', $request->classe);
            })
            ->groupBy(
                DB::raw($dateFormat),
                'Student.id',
                '_User.firstName',
                '_User.lastName',
                'Classe.name',
                'MisconductReport.status',
                'Student.classeId'
            )
            ->limit(50)
            ->get();

        return response()->json(['reportData' => $reportData]);
    }
}
