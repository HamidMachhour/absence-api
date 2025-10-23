<?php

namespace App\Http\Controllers\Api\V1;

use Exception;
use App\Models\User;
use App\Models\Absence;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\AbsenceStudent;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AbsenceController extends Controller
{

    public function ofUser(Request $request, User $user, $page_size = 40)
    {
        if (!Auth::check()) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }
        if (Auth::user()->id != $user->id) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }
        try {

            if ($request->filled("page_size"))
                $page_size = $request->input("page_size");



            $absences = Absence::whereIn('id', function ($query) use ($user) {
                $query->select('absenceId')
                    ->from('AbsenceStudent')
                    ->join('Student', 'Student.id', '=', 'AbsenceStudent.studentId')
                    ->where('userId', $user->id)
                    ->distinct();
            })
                ->filter($request)
                ->with('seance')
                ->paginate($page_size);
            /*
            $absences = Absence::select('Absence.*')
                ->join('AbsenceStudent', 'Absence.id', '=', 'AbsenceStudent.absenceId')
                ->join('Student', 'AbsenceStudent.studentId', '=', 'Student.id')
                ->join('_User', 'Student.userId', '=', '_User.id')
                ->where('_User.id', $user->id)
                ->distinct()
                ->paginate($page_size);*/

            return $absences;
        } catch (Exception $e) {
            return response()->json(['message' => __('vocab.empty') . $e->getMessage()], 404);
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
            return response()->json(['message' => __('vocab.empty') . $e->getMessage()], 404);
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
                $absence->absenceStudents()->delete();
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

    public function getChartData(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }

        $absenceData = Student::select(
            DB::raw('strftime("%Y-%m-%d",AbsenceStudent.created_at) as date'),
            DB::raw('Student.id as student_id'),
            DB::raw('_User.firstName'),
            DB::raw('_User.lastName'),
            DB::raw('Student.classeId as classeId'),
            DB::raw('Classe.name as classe_name'),
            DB::raw('count(*) as nb_absences'),
            DB::raw('sum(plage) as sum_plages'),
            DB::raw('count(justification) as justifie'),
            DB::raw('count(*)-count(justification) as non_justifie')

        )
            ->join('Classe', 'Classe.id', '=', 'Student.classeId')
            ->join('AbsenceStudent', 'AbsenceStudent.studentId', '=', 'Student.id')
            ->join('_User', '_User.id', '=', 'Student.userId')
            ->whereNull('AbsenceStudent.deleted_at')
            ->whereNull('_User.deleted_at')
            ->whereNull('Student.deleted_at')
            ->whereNull('Classe.deleted_at')
            ->when($request->student, function ($query) use ($request) {
                return $query->where('Student.id', $request->student);
            })
            ->when($request->classe, function ($query) use ($request) {
                return $query->where('Student.classeId', $request->classe);
            })
            ->when($request->from, function ($query) use ($request) {
                return $query->whereDate('AbsenceStudent.created_at', '>=', $request->from);
            })
            ->when($request->to, function ($query) use ($request) {
                return $query->whereDate('AbsenceStudent.created_at', '<=', $request->to);
            })
            ->groupBy(
                DB::raw('strftime("%Y-%m-%d",AbsenceStudent.created_at)'),
                DB::raw('Student.id'),
                DB::raw('_User.firstName'),
                DB::raw('_User.lastName'),
                DB::raw('Student.classeId'),
                DB::raw('Classe.name')
            )
            ->limit(50)
            ->get();
        return response()->json(['absenceData' => $absenceData]);
    }
}
