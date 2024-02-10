<?php

namespace App\Http\Controllers\Web;

use Exception;
use App\Models\Classe;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\StudentCandidate;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\V1\StudentCandidateController as V1Controller;

class StudentCandidateController extends Controller
{
    // Show all student Candidates
    public function index(Request $request)
    {
        try {
            $v1Controller = new V1Controller();
            if (Auth::check())
                $request['manager'] = auth()->user()->id;

            $response = $v1Controller->index($request);

            if (method_exists($response, 'status') && $response->status() == 404)
                return view('error', ["message" => ' غير متوفر']);

            return view('studentCandidates.index', [
                'students' => $response
            ]);
        } catch (Exception $e) {
            return view('error', ["message" => '  غير متوفر' . $e->getMessage()]);
        }
    }

    // Show all authenticated user's studentCandidates
    public function manage(Request $request, Classe $classe)
    {
        if (!Auth::user()->hasRole('admin') && Auth::user()->id !== $classe->userId) {
            return redirect()->route('home.route')->with('message',  'غير مسموح');
        }
        $v1Controller = new V1Controller();
        $request['classeId'] = $classe->id;

        $response = $v1Controller->show($request);
        // Get the HTTP status code from the response
        if (method_exists($response, 'status') && $response->status() == 404)
            return view('error', ["message" => ' غير متوفر']);

        return view('studentCandidates.manage', [
            'classe' => $classe,
            'students' => $response
        ]);
    }

    //Show single student
    public function show(StudentCandidate $student)
    {
        return view('studentCandidates.show', [
            'student' => $student
        ]);
    }

    // Store student Data
    public function store(Request $request)
    {
        $controller = new V1Controller();
        $response = $controller->store($request);
        // Get the HTTP status code from the response
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array
        if ($status_code === 201) {
            $message = isset($data['message']) ? $data['message'] : 'تم ارسال الطلب بنجاح!';
            return redirect()->route('classes.show', ['classe' => $request->get('classeId')])->with('message', $message);
        } else {
            $message = isset($data['message']) ? $data['message'] : 'لم تتم العملية بنجاح، المرجو اعادة المحاولة';
            return back()->withErrors(['message' => $message]);;
        }
    }

    // Delete student candidate
    public function accept(StudentCandidate $student)
    {
        if (!Auth::user()->hasRole('admin') && Auth::user()->id !== $student->classe->userId) {
            return redirect()->route('home.route')->with('message',  'غير مسموح');
        }
        $controller = new V1Controller();
        $response = $controller->accept($student);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array


        if ($status_code === 200) {
            $message = isset($data['message']) ? $data['message'] : 'تم قبول طلب الالتحاق بنجاح';
        } else {
            $message = isset($data['message']) ? $data['message'] : 'لم تتم العملية بنجاح، المرجو اعادة المحاولة';
        }
        return redirect()->route('candidates.manage', ['classe' => $student->classeId])->with('message', $message);
    }
    // Delete student candidate
    public function destroy(StudentCandidate $student)
    {
        if (!Auth::user()->hasRole('admin') && Auth::user()->id !== $student->classe->userId) {
            return redirect()->route('home.route')->with('message',  'غير مسموح');
        }
        $controller = new V1Controller();
        $response = $controller->destroy($student);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array


        if ($status_code === 200) {
            $message = isset($data['message']) ? $data['message'] :  'تم الحدف بنجاح';
        } else {
            $message = isset($data['message']) ? $data['message'] : 'لم تتم العملية بنجاح، المرجو اعادة المحاولة';
        }
        return redirect()->route('candidates.manage', ['classe' => $student->classeId])->with('message', $message);
    }
}
