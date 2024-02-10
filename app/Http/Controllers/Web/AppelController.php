<?php

namespace App\Http\Controllers\Web;

use App\Models\Appel;


use App\Models\Seance;
use App\Models\Student;
use App\Models\AppelStudent;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\V1\AppelController as V1AppelController;
use Carbon\Carbon;

class AppelController extends Controller
{

    public function checklist(Seance $seance)
    {
        if (auth()->user()->id === $seance->profId) {

                $students = Student::where('classeId', $seance->classeId)
                ->whereDoesntHave('appels',function($q) use($seance){
                    $q->where('seanceId',$seance->id)
                    ->whereDate('created_at',Carbon::today());
                })->paginate(40);

            return view('appels.checklist', [
                'seance' => $seance,
                'students' => $students
            ]);
        } else {
            return route('home.route');
        }
    }
    public function show(Appel $appel)
    {
        if (auth()->user()->id === $appel->userId) {
            return view('appels.show', [
                'appel' => $appel
            ]);
        } else {
            return route('home.route');
        }
    }

    public function savePlage(Appel $appel, AppelStudent $appel_student)
    {
        $controller = new V1AppelController();
        $response = $controller->savePlage($appel, $appel_student);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array

        if ($status_code === 403)
            return redirect()->route('users.login');

        if ($status_code === 200) {
            $message = isset($data['message']) ? $data['message'] : 'تم تأكيد الغياب بنجاح';
            return back()->with('message', $message);
        } else {
            $message = isset($data['message']) ? $data['message'] : 'لم يتم تأكيد الغياب';
            return back()->withErrors(['message' => $message]);;
        }
    }
    public function saveJustification(Appel $appel, AppelStudent $appel_student)
    {
        $controller = new V1AppelController();
        $response = $controller->saveJustification($appel, $appel_student);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array

        if ($status_code === 403)
            return redirect()->route('users.login');

        if ($status_code === 200) {
            $message = isset($data['message']) ? $data['message'] : 'تم تأكيد الغياب بنجاح';
            return back()->with('message', $message);
        } else {
            $message = isset($data['message']) ? $data['message'] : 'لم يتم تأكيد الغياب';
            return back()->withErrors(['message' => $message]);;
        }
    }
    public function appel_to_absence(Appel $appel)
    {
        $controller = new V1AppelController();
        $response = $controller->appel_to_absence($appel);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array

        if ($status_code === 403)
            return redirect()->route('appels.show',['appel'=>$appel]);

        if ($status_code === 200) {
            $message = isset($data['message']) ? $data['message'] : 'تم تأكيد الغياب بنجاح';
            return back()->with('message', $message);
        } else {
            $message = isset($data['message']) ? $data['message'] : 'لم يتم تأكيد الغياب';
            return back()->withErrors(['message' => $message]);;
        }
    }


    public function clear(Appel $appel)
    {
        $controller = new V1AppelController();
        $response = $controller->clear($appel);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array

        if ($status_code === 403)
            return redirect()->route('users.login');

        if ($status_code === 200) {
            $message = isset($data['message']) ? $data['message'] : 'Appel cleared!';
            return back()->with('message', $message);
        } else {
            $message = isset($data['message']) ? $data['message'] : 'Appel not cleared!';
            return back()->withErrors(['message' => $message]);;
        }
    }

    public function popStudent(Appel $appel, AppelStudent $appel_student)
    {
        $controller = new V1AppelController();
        $response = $controller->popStudent($appel, $appel_student);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array

        if ($status_code === 403)
            return redirect()->route('users.login');

        if ($status_code === 200) {
            $message = isset($data['message']) ? $data['message'] : 'deleted from appel!';
            return back()->with('message', $message);
        } else {
            $message = isset($data['message']) ? $data['message'] : 'Not deleted from appel!';
            return back()->withErrors(['message' => $message]);;
        }
    }

    public function pushStudent(Request $request)
    {

        $controller = new V1AppelController();
        $response = $controller->pushStudent($request);

        // Get the HTTP status code from the response
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array

        if ($status_code === 403)
            return redirect()->route('users.login');

        if ($status_code === 200) {
            $message = isset($data['message']) ? $data['message'] : 'تم التسجيل بنجاح';
            return back()->with('message', $message);
        } else {
            $message = isset($data['message']) ? $data['message'] : 'فشلت العملية. المرجو اعادة المحاولة';
            return back()->withErrors(['message' => $message]);;
        }
    }

    public function pushAllStudents(Request $request)
    {

        $controller = new V1AppelController();
        $response = $controller->pushAllStudents($request);

        // Get the HTTP status code from the response
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array

        if ($status_code === 403)
            return redirect()->route('users.login');

        if ($status_code === 200) {
            $message = isset($data['message']) ? $data['message'] : 'تم التسجيل بنجاح';
            return back()->with('message', $message);
        } else {
            $message = isset($data['message']) ? $data['message'] : 'فشلت العملية. المرجو اعادة المحاولة';
            return back()->withErrors(['message' => $message]);;
        }
    }
}
