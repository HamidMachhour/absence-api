<?php

namespace App\Http\Controllers\Api\V1;

use Exception;
use Carbon\Carbon;
use App\Models\Appel;
use App\Models\Seance;
use App\Models\Absence;
use App\Models\Student;
use App\Models\AppelStudent;
use Illuminate\Http\Request;
use App\Models\AbsenceStudent;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AppelController extends Controller
{

    public function clear(Appel $appel)
    {

        if (!Auth::check()) {
            return response()->json(['message' => 'غير مسموح، المرجو تسجيل الدخول'], 403);
        }

        if (auth()->user()->id !== $appel->userId) {
            return response()->json(['message' => 'غير مسموح!'], 403);
        }

        $appel->appelStudents()->delete();

        return response()->json(['message' => 'حدف بنجاح'], 200);
    }

    public function appel_to_absence(Appel $appel)
    {

        if (!Auth::check()) {
            return response()->json(['message' => 'غير مسموح، المرجو تسجيل الدخول'], 403);
        }

        if (auth()->user()->id !== $appel->userId) {
            return response()->json(['message' => 'غير مسموح!'], 403);
        }

        try {

            DB::transaction(function () use ($appel) {

                $seance_absence = array();
                foreach ($appel->appelStudents as $appel_student) {

                    if (!array_key_exists($appel_student->seanceId, $seance_absence)) {
                        $absence = Absence::create([
                            'userId' => $appel->userId,
                            'seanceId' => $appel_student->seanceId,
                            'created_at' =>$appel_student->created_at
                        ]);
                        $seance_absence[$appel_student->seanceId] = $absence;
                    }
                    $absence = $seance_absence[$appel_student->seanceId];

                    AbsenceStudent::create([
                        'studentId' => $appel_student->studentId,
                        'absenceId' => $absence->id,
                        'plage' => $appel_student->plage,
                        'justification' => $appel_student->justification,
                        'created_at' =>$appel_student->created_at
                    ]);
                    $appel_student->delete();
                }
            });


            return response()->json(['message' => 'Absence success'], 200);
        } catch (Exception $e) {
            dd($e);
            return response()->json(['message' => 'Absence failed!' . $e->getMessage()], 403);
        }
    }

    public function popStudent(Appel $appel, AppelStudent $appel_student)
    {

        if (!Auth::check()) {
            return response()->json(['message' => 'غير مسموح، المرجو تسجيل الدخول'], 403);
        }

        if (auth()->user()->id !== $appel->userId) {
            return response()->json(['message' => 'غير مسموح!'], 403);
        }

        if ($appel_student->appelId === $appel->id) {
            $appel_student->delete();
            return response()->json(['message' => 'حدف بنجاح'], 200);
        }

        return response()->json(['message' => 'فشل الحدف، كرر مرة اخرى من فضلك'], 404);
    }

    public function pushStudent(Request $request)
    {

        if (!Auth::check()) {
            return response()->json(['message' => 'غير مسموح، المرجو تسجيل الدخول'], 403);
        }
        try {
            // Validate the request data
            $validatedData = $request->validate([
                'studentId' => 'required|string',
                'seanceId' => 'required|string',
            ]);

            $student = Student::findOrFail($validatedData['studentId']);
            $seance = Seance::findOrFail($validatedData['seanceId']);

            $plage = $request->filled('plage') ?
                $request->input('plage') : $seance->plageInMinutes();

            $date = $request->filled('date') ?
                Carbon::parse($request->input('date')) : Carbon::today();

            $user = Auth::user();
            $appel = $user->appel();

            if (
                $appel && $user->id === $seance->profId
                && $student->classeId === $seance->classeId
                && $appel->pushStudent($seance, $student->id, $plage, $date)
            )
                return response()->json(['message' => 'تمت الاضافة بنجاح'], 200);

            return response()->json(['message' => 'فشلت الاضافة'], 404);
        } catch (Exception $exception) {
            return response()->json(['message' => 'فشلت الاضافة' . $exception->getMessage()], 404);
        }
    }

    public function pushAllStudents(Request $request)
    {

        if (!Auth::check()) {
            return response()->json(['message' => 'غير مسموح، المرجو تسجيل الدخول'], 403);
        }
        try {
            // Validate the request data
            $validatedData = $request->validate([
                'seanceId' => 'required|string',
            ]);

            $seance = Seance::findOrFail($validatedData['seanceId']);

            $plage = $request->filled('plage') ?
                $request->input('plage') : $seance->plageInMinutes();

            $date = $request->filled('date') ?
                Carbon::parse($request->input('date')) : Carbon::today();

            $user = Auth::user();
            $appel = $user->appel();

            if (
                $appel && $user->id === $seance->profId
                && $appel->pushAllStudents($seance, $plage, $date)
            )
                return response()->json(['message' => 'تمت الاضافة بنجاح'], 200);

            return response()->json(['message' => 'فشلت الاضافة'], 404);
        } catch (Exception $exception) {
            return response()->json(['message' => 'فشلت الاضافة' . $exception->getMessage()], 404);
        }
    }

    public function savePlage(Appel $appel, AppelStudent $appel_student)
    {

        if (!Auth::check()) {
            return response()->json(['message' => 'غير مسموح، المرجو تسجيل الدخول'], 403);
        }
        if (auth()->user()->id !== $appel->userId) {
            return response()->json(['message' => 'غير مسموح!'], 403);
        }
        try {
            // Validate the request data
            $validatedData = request()->validate([
                'plage' => 'required|numeric'
            ]);

            if ($appel_student->appelId === $appel->id) {
                $plage = $validatedData['plage'];

                $seance = $appel_student->seance;

                $seance_plage = $seance->plageInMinutes();
                $plage = ($plage <= 0 || $plage > $seance_plage) ? $seance_plage : $plage;

                $appel_student->update(['plage' => $plage]);
                return response()->json(['message' => 'تم تعديل المدة الزمنية'], 200);
            }

            return response()->json(['message' => 'فشل التعديل'], 404);
        } catch (Exception $exception) {
            return response()->json(['message' => 'فشل التعديل، ' . $exception->getMessage()], 404);
        }
    }


    public function saveJustification(Appel $appel, AppelStudent $appel_student)
    {

        if (!Auth::check()) {
            return response()->json(['message' => 'غير مسموح، المرجو تسجيل الدخول'], 403);
        }
        if (auth()->user()->id !== $appel->userId) {
            return response()->json(['message' => 'غير مسموح!'], 403);
        }
        try {
            // Validate the request data
            $validatedData = request()->validate([
                'justification' => 'nullable|string'
            ]);


            $justification = $validatedData['justification'];
            if ($appel_student->appelId === $appel->id) {
                $appel_student->update(['justification' => $justification]);
                return response()->json(['message' => 'تم تعديل التبرير بنجاح'], 200);
            }

            return response()->json(['message' => 'فشل التعديل '], 404);
        } catch (Exception $exception) {
            dd($exception);
            return response()->json(['message' => 'فشل التعديل، ' . $exception->getMessage()], 404);
        }
    }
}
