<?php

namespace App\Http\Controllers\Web;

use DateTime;
use Illuminate\Http\JsonResponse;
use DateInterval;
use App\Models\_Day;
use App\Models\User;
use App\Models\Classe;
use App\Models\Seance;

use App\Models\Subject;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\V1\SeanceController as V1SeanceController;

class SeanceController extends Controller
{

    // Declare a static property to hold the array
    protected static array $sharedHours = [];

    // The constructor is called once when the controller is instantiated
    public function __construct()
    {
        // Check if the static array has already been populated
        // This ensures the function is called only once, even if
        // multiple instances of the controller are created during the request lifecycle.
        if (empty(self::$sharedHours)) {
            self::$sharedHours = $this->hours();
        }
    }

    // Show all seances
    public function index(Request $request, V1SeanceController $v1Controller)
    {
        $response = $v1Controller->index($request);

        if ($response instanceof JsonResponse) {
            $status = $response->getStatusCode();
            $data = $response->getData(); // Get the underlying data object from the JSON response

            // Test on response status for errors
            if ($status === 403 || $status === 404) {
                $message = $data->message ?? __('vocab.not_authorized');
                return redirect()->route('error', ['message' => $message]);
            }
        }

        $params = $this->prepareFilterParams($request, $response);
        return view('seances.index', [
            'seances' => $response,
            'params' => $params
        ]);
    }

    // Show all authenticated user's seances
    public function manage(Request $request, V1SeanceController $v1Controller)
    {

        $response = $v1Controller->show($request);

        $teachers = User::teachers();
        $classes = Classe::all();
        $subjects = Subject::all();
        $jours = _Day::all();
        
        if ($response instanceof JsonResponse) {
            $status = $response->getStatusCode();
            $data = $response->getData(); // Get the underlying data object from the JSON response

            // Test on response status for errors
            if ($status === 403 || $status === 404) {
                $message = $data->message ?? __('vocab.not_authorized');
                return redirect()->route('error', ['message' => $message]);
            }
        }

        $params = $this->prepareFilterParams($request, $response);
        return view('seances.manage', [
            'seances' => $response,
            'params' => $params,
            'teachers' => $teachers,
            'classes' => $classes,
            'subjects' => $subjects,
            'days' => $jours,
            'plages' => self::$sharedHours,
        ]);
    }
    public function prepareFilterParams(Request $request, $response)
    {

        $params = [];
        if (!$response || count($response) == 0)
            return $params;

        if ($request->filled('classeId')) {
            $params['classeId'] = __('vocab.class') . ':' . $response->first()->classe->classeName();
        }
        if ($request->filled('teacherId')) {
            $params['teacherId'] = __('vocab.teacher') . ':' . $response->first()->teacher->fullName();
        }
        if ($request->filled('subjectId')) {
            $params['subjectId'] = __('vocab.subject') . ':' . $response->first()->subject->subjectName();
        }
        if ($request->filled('dayId')) {
            $params['dayId'] = __('vocab.day') . ':' . $response->first()->day->dayName();
        }
        if ($request->filled('search')) {
            $params['search'] = __('vocab.search') . $request->search;
        }
        if ($request->filled('salle')) {
            $params['salle'] = __('vocab.room') . ':' . $request->salle;
        }

        return $params;
    }
    //Show single seance
    public function show(Seance $seance)
    {
        return view('seances.show', [
            'seance' => $seance
        ]);
    }

    // Show Create Form
    public function create()
    {

        $teachers = User::withRole("teacher")->get();
        $classes = Classe::all();
        $subjects = Subject::all();
        $jours = _Day::all();
        return view(
            'seances.create',
            [
                'teachers' => $teachers,
                'classes' => $classes,
                'subjects' => $subjects,
                'jours' => $jours,
                'hours' => self::$sharedHours,
            ]
        );
    }

    public static function hours(): array
    {
        $startTime = new DateTime('00:00');
        $endTime = new DateTime('23:59');
        $interval = new DateInterval('PT5M'); // Interval of 5 min

        $hours = [];
        for ($current = $startTime; $current <= $endTime; $current->add($interval)) {
            $hours[] = $current->format('H:i');
        }
        $hours[] = $endTime->format('H:i');
        return $hours;
    }

    // Store store Data
    public function store(Request $request, V1SeanceController $controller)
    {

        $response = $controller->store($request);
        // Get the HTTP status code from the response
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array
        if ($status_code === 201) {
            $message = isset($data['message']) ? $data['message'] : __('vocab.action_success');
            return redirect()->route('seances.manage')->with('message', $message);
        } else {
            $message = isset($data['message']) ? $data['message'] : __('vocab.action_failure') . ' ' . __('vocab.try_again');
            return back()->withErrors(['message' => $message]);;
        }
    }

    // Show Edit Form
    public function edit(Seance $seance)
    {
        if (!Auth::user()->hasRole('admin')) {
            return redirect()->route('home')->with('message',  __('vocab.not_authorized'));
        }
        $teachers = User::withRole("teacher")->get();
        $classes = Classe::all();
        $subjects = Subject::all();
        $jours = _Day::all();

        return view(
            'seances.edit',
            [
                'seance' => $seance,
                'teachers' => $teachers,
                'classes' => $classes,
                'subjects' => $subjects,
                'jours' => $jours,
                'hours' => self::$sharedHours,
            ]
        );
    }

    // Update store Data
    public function update(Request $request, Seance $seance)
    {
        if (!Auth::user()->hasRole('admin')) {
            return redirect()->route('error', ['message' =>  __('vocab.not_authorized')]);
        }

        $controller = new V1SeanceController();
        $response = $controller->update($request, $seance);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array


        if ($status_code === 200) {
            // update successful
            $message = isset($data['message']) ? $data['message'] : __('vocab.update_success');
            //get token and save
        } else {
            $message = isset($data['message']) ? $data['message'] : __('vocab.update_failure') . ' ' . __('vocab.try_again');
        }
        return redirect()->route('seances.edit', ['seance' => $seance])->with('message', $message);
    }

    // Delete seance
    public function destroy(Seance $seance)
    {
        if (!Auth::user()->hasRole('admin')) {
            return redirect()->route('home')->with('message',  __('vocab.not_authorized'));
        }
        $controller = new V1SeanceController();
        $response = $controller->destroy($seance);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array


        if ($status_code === 200) {
            $message = isset($data['message']) ? $data['message'] : __('vocab.delete_success');
        } else {
            $message = isset($data['message']) ? $data['message'] : __('vocab.delete_failed');
        }
        return redirect()->route('seances.manage')->with('message', $message);
    }

    //Destroy all seances
    public function destroyAll(Request $request)
    {
        if (!Auth::user()->hasRole('admin')) {
            return redirect()->route('home')->with('message',  __('vocab.not_authorized'));
        }
        $controller = new V1SeanceController();
        $response = $controller->destroyAll($request);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array


        if ($status_code === 200) {
            $message = isset($data['message']) ? $data['message'] : __('vocab.delete_success');
        } else {
            $message = isset($data['message']) ? $data['message'] : __('vocab.delete_failed');
        }
        return redirect()->route('seances.manage')->with('message', $message);
    }

    public function export(Request $request, V1SeanceController $controller)
    {
        if (!Auth::user()->hasRole('admin') && !Auth::user()->hasRole('manager')) {
            return redirect()->route('home')->with('message',  __('vocab.not_authorized'));
        }

        $response = $controller->export($request);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array

        if ($status_code === 200) {
            return $response;
        }

        $message = isset($data['message']) ? $data['message'] : __('vocab.action_failure');

        return redirect()->route('seances.manage')->with('message', $message);
    }

    public function importForm()
    {

        if (!Auth::user()->hasRole('admin')) {
            return redirect()->route('home')->with('message', __('vocab.not_authorized'));
        }
        return view('seances.import');
    }

    public function import(Request $request, V1SeanceController $controller)
    {
        // Authorization check
        if (!Auth::user()->hasRole('admin')) {
            return redirect()->route('home')->with('message',  __('vocab.not_authorized'));
        }

        $response = $controller->import($request);
        $status_code = $response->getStatusCode();

        // Check if the response from V1SeanceController is a successful download response
        if ($status_code === 200 && $response->headers->has('Content-Disposition')) {
            return $response; // Directly return the file response to the browser
        }

        // If it's not a download response (e.g., an error response),
        // then process it to get the message and redirect.
        $responseContent = $response->content();
        $data = json_decode($responseContent, true);

        if ($status_code === 200) {
            $message = isset($data['message']) ? $data['message'] : __('vocab.seances_imported');
            return redirect()->route('seances.manage')->with('message', $message);
        } else {
            $message = isset($data['message']) ? $data['message'] : __('vocab.action_failure');
            return redirect()->route('seances.manage')->with('message', $message);
        }
    }
}
