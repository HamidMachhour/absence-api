<?php

namespace App\Http\Controllers\Web;

use App\Models\Absence;
use Illuminate\Http\Request;
use App\Models\AbsenceStudent;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\V1\AbsenceController as V1AbsenceController;
use App\Models\User;

class AbsenceController extends Controller
{

    public function ofUser(Request $request, User $user, V1AbsenceController $v1Controller)
    {
        $response = $v1Controller->ofUser($request, $user);

        // 1. Check if the response is an explicit error (JsonResponse)
        if ($response instanceof JsonResponse) {
            $status = $response->getStatusCode();
            $data = $response->getData(); // Get the underlying data object from the JSON response

            // Test on response status for errors
            if ($status === 403 || $status === 404) {
                $message = $data->message ?? __('vocab.not_authorized');
                return redirect()->route('error', ['message' => $message]);
            }
        }

        // 2. If it is not a JsonResponse, it is the successful Paginator object.
        // We can safely call Paginator methods like total()
        $absences = $response; // Rename for clarity

        $count = $absences->total();
        $params = $this->prepareFilterParams($request, $absences);

        return view('absences.index', [
            'absences' => $absences,
            'totalCount' => $count,
            'params' => $params
        ]);
    }

    public function index(Request $request, V1AbsenceController $v1Controller)
    {
        $response = $v1Controller->index($request);

        // 1. Check if the response is an explicit error (JsonResponse)
        if ($response instanceof JsonResponse) {
            $status = $response->getStatusCode();
            $data = $response->getData(); // Get the underlying data object from the JSON response

            // Test on response status for errors
            if ($status === 403 || $status === 404) {
                $message = $data->message ?? __('vocab.not_authorized');
                return redirect()->route('error', ['message' => $message]);
            }
        }

        // 2. If it is not a JsonResponse, it is the successful Paginator object.
        $absences = $response; // Rename for clarity

        $params = $this->prepareFilterParams($request, $absences);
        $count = $absences->total();

        return view('absences.index', [
            'absences' => $absences,
            'totalCount' => $count,
            'params' => $params
        ]);
    }

    public function prepareFilterParams(Request $request, $response)
    {

        $params = [];
        if (!$response || $response->isEmpty())
            return $params;

        if ($request->filled('classeId')) {
            $params['classeId'] = __('vocab.class') . ':' . $response->first()->seance->classe->classeName();
        }
        if ($request->filled('teacherId')) {
            $params['teacherId'] = __('vocab.teacher') . ':' . $response->first()->seance->teacher->fullName();
        }
        if ($request->filled('subjectId')) {
            $params['subjectId'] = __('vocab.subject') . ':' . $response->first()->seance->subject->subjectName();
        }
        if ($request->filled('dayId')) {
            $params['dayId'] = __('vocab.day') . ':' . $response->first()->seance->day->dayName();
        }
        if ($request->filled('salle')) {
            $params['salle'] = __('vocab.room') . ':' . $response->first()->seance->salle;
        }
        if ($request->filled('date_absence')) {
            $params['date_absence'] = '>> ' . $request->date_absence;
        }
        return $params;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Absence  $absence
     * @return \Illuminate\Http\Response
     */
    public function show(Absence $absence)
    {
        return view('absences.show', [
            'absence' => $absence
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Absence  $absence
     * @return \Illuminate\Http\Response
     */
    public function destroy(Absence $absence)
    {
        $controller = new V1AbsenceController();

        $response = $controller->destroy($absence);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array

        if ($status_code !== 200) {
            $message = isset($data['message']) ? $data['message'] : __('vocab.not_authorized');
        } else
            $message = isset($data['message']) ? $data['message'] : __('vocab.delete_success');

        return redirect()->route("absences.index")->with('message', $message);
    }

    public function popStudent(Absence $absence, AbsenceStudent $absence_student)
    {
        $controller = new V1AbsenceController();
        $response = $controller->popStudent($absence, $absence_student);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array

        if ($status_code === 200) {
            $message = isset($data['message']) ? $data['message'] : __('vocab.delete_success');
        } else {
            $message = isset($data['message']) ? $data['message'] : __('vocab.delete_failed');
        }
        return back()->with('message', $message);
    }

    public function saveJustification(Absence $absence, AbsenceStudent $absence_student)
    {
        $controller = new V1AbsenceController();
        $response = $controller->saveJustification($absence, $absence_student);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array

        if ($status_code === 200) {
            $message = isset($data['message']) ? $data['message'] : __('vocab.update_success');
        } else {
            $message = isset($data['message']) ? $data['message'] : __('vocab.update_failure');
        }
        return back()->with('message', $message);
    }

    public function getChartAbsenceData(Request $request, V1AbsenceController $v1Controller)
    {
        return $v1Controller->getChartAbsenceData($request);
    }

     public function getChartMisconductReportData(Request $request, V1AbsenceController $v1Controller)
    {
        return $v1Controller->getChartMisconductReportData($request);
    }
}
