<?php

namespace App\Http\Controllers\Web;

use DateTime;
use ArrayObject;
use DateInterval;
use App\Models\Jour;
use App\Models\User;
use App\Models\Classe;
use App\Models\Seance;
use League\Csv\Reader;
use App\Models\Matiere;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\V1\SeanceController as V1SeanceController;

class SeanceController extends Controller
{

    // Show all seances
    public function index(Request $request)
    {
        $v1Controller = new V1SeanceController();
        $response = $v1Controller->index($request);

        $params = $this->prepareFilterParams($request, $response);
        return view('seances.index', [
            'seances' => $response,
            'params' => $params
        ]);
    }

    // Show all authenticated user's seances
    public function manage(Request $request)
    {

        $v1Controller = new V1SeanceController();
        $response = $v1Controller->show($request);

        $params = $this->prepareFilterParams($request, $response);
        return view('seances.manage', [
            'seances' => $response,
            'params' => $params
        ]);
    }
    public function prepareFilterParams(Request $request, $response)
    {

        $params = [];
        if (!$response || count($response) == 0)
            return $params;

        if ($request->filled('classeId')) {
            $params['classeId'] = 'القسم:' . $response->first()->classe->name;
        }
        if ($request->filled('profId')) {
            $params['profId'] = 'الاستاد:' . $response->first()->professeur->firstName;
        }
        if ($request->filled('matiereId')) {
            $params['matiereId'] = 'المادة:' . $response->first()->matiere->name;
        }
        if ($request->filled('jourId')) {
            $params['jourId'] = 'اليوم:' . $response->first()->jour->name;
        }
        if ($request->filled('search')) {
            $params['search'] = 'بحث ' . $request->search;
        }

        return $params;
    }
    //Show single seance
    public function show(Seance $seance)
    {
        //dd($seance);
        return view('seances.show', [
            'seance' => $seance
        ]);
    }

    // Show Create Form
    public function create()
    {

        $teachers = User::withRole("teacher")->get();
        $classes = Classe::all();
        $matieres = Matiere::all();
        $jours = Jour::all();
        return view(
            'seances.create',
            [
                'teachers' => $teachers,
                'classes' => $classes,
                'matieres' => $matieres,
                'jours' => $jours,
                'hours' => $this->hours(),
            ]
        );
    }

    public static function hours()
    {
        $startTime = new DateTime('08:30');
        $endTime = new DateTime('18:30');
        $interval = new DateInterval('PT1H'); // Interval of 1 hour

        $hours = new ArrayObject();
        for ($current = $startTime; $current <= $endTime; $current->add($interval)) {
            $hours->append($current->format('H:i'));
        }
        return $hours;
    }
    // Store store Data
    public function store(Request $request)
    {
        $controller = new V1SeanceController();
        $response = $controller->store($request);
        // Get the HTTP status code from the response
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array
        if ($status_code === 201) {
            $message = isset($data['message']) ? $data['message'] : 'تمت الاضافة بنجاح';
            return redirect()->route('seances.manage')->with('message', $message);
        } else {
            $message = isset($data['message']) ? $data['message'] : 'لم تتم العملية بنجاح، المرجو اعادة المحاولة';
            return back()->withErrors(['message' => $message]);;
        }
    }

    // Show Edit Form
    public function edit(Seance $seance)
    {
        if (Auth::user()->id !== $seance->userId && !Auth::user()->hasRole('admin')) {
            return redirect()->route('home.route')->with('message',  'غير مسموح');
        }
        $teachers = User::withRole("teacher")->get();
        $classes = Classe::all();
        $matieres = Matiere::all();
        $jours = Jour::all();

        return view(
            'seances.edit',
            [
                'seance' => $seance,
                'teachers' => $teachers,
                'classes' => $classes,
                'matieres' => $matieres,
                'jours' => $jours,
                'hours' => $this->hours(),
            ]
        );
    }

    // Update store Data
    public function update(Request $request, Seance $seance)
    {
        if (Auth::user()->id !== $seance->userId && !Auth::user()->hasRole('admin')) {
            return redirect()->route('error.route', ['message' =>  'غير مسموح']);
        }

        $controller = new V1SeanceController();
        $response = $controller->update($request, $seance);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array


        if ($status_code === 200) {
            // update successful
            $message = isset($data['message']) ? $data['message'] : 'تمت التعديل بنجاح';
            //get token and save
        } else {
            $message = isset($data['message']) ? $data['message'] : 'لم يتم التعديل بنجاح';
        }
        return redirect()->route('seances.edit', ['seance' => $seance])->with('message', $message);
    }

    // Delete store
    public function destroy(Seance $seance)
    {
        if (Auth::user()->id !== $seance->userId && !Auth::user()->hasRole('admin')) {
            return redirect()->route('home.route')->with('message',  'غير مسموح');
        }
        $controller = new V1SeanceController();
        $response = $controller->destroy($seance);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array


        if ($status_code === 200) {
            $message = isset($data['message']) ? $data['message'] : 'تم الحدف بنجاح';
        } else {
            $message = isset($data['message']) ? $data['message'] : 'الحدف لم تتم بنجاح';
        }
        return redirect()->route('seances.manage')->with('message', $message);
    }


    public function export(Seance $seance)
    {
        if (Auth::user()->id !== $seance->userId && !Auth::user()->hasRole('admin')) {
            return redirect()->route('home.route')->with('message',  'غير مسموح');
        }
        $controller = new V1SeanceController();
        $response = $controller->export($seance);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array

        if ($status_code === 200) {
            return $response;
        }

        $message = isset($data['message']) ? $data['message'] : 'Action failed.';

        return redirect()->route('seances.manage')->with('message', $message);
    }

    public function importForm(Seance $seance)
    {

        if (Auth::user()->id !== $seance->userId && !Auth::user()->hasRole('admin')) {
            return redirect()->route('home.route')->with('message', 'Import unauthorized for this store!');
        }
        return view('seances.import', ['seance' => $seance]);
    }

    public function import(Request $request, Seance $seance)
    {
        if (Auth::user()->id !== $seance->userId && !Auth::user()->hasRole('admin')) {
            return redirect()->route('home.route')->with('message',  'غير مسموح');
        }

        $controller = new V1SeanceController();
        $response = $controller->import($request, $seance);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array


        if ($status_code === 200) {
            //  return $response;
            $message = isset($headers['added']) ? $headers['added'] . ' students added successfully!' : 'Import students successully.';
            return redirect()->route('students.manage', ['seance' => $seance->id])->with('message', $message);
        } else {
            $message = isset($data['message']) ? $data['message'] : 'Action failed.';
            return redirect()->route('students.manage', ['seance' => $seance->id])->with('message', $message);
        }
    }
}
