<?php

namespace App\Http\Controllers\Web;

use Exception;
use App\Models\Classe;
use Illuminate\Http\Request;
use App\Models\Enrollment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\V1\EnrollmentController as V1EnrollmentController;
use App\Models\Student;
use App\Models\User;


class EnrollmentController extends Controller
{
    // Show all enrollment Candidates
    public function index(Request $request, V1EnrollmentController $v1Controller)
    {
        try {

            $total =  $v1Controller->totalRows($request);
            $response = $v1Controller->index($request);

            $managers = User::withRole('manager')->get();

            $classes = Classe::when($request->filled('manager'), function ($q) use ($request) {
                $q->where('userId', $request->manager);
            })->get();
            /*
            $students = User::when($request->filled('classeId'), function ($q) use ($request) {
                $q->whereHas(
                    'students',
                    function ($student) use ($request) {
                        $student->where('classeId', $request->classeId);
                    }
                );
            })->get();
            */
            return view('enrollments.index', [
                'enrollments' => $response,
                'total' => $total,
                'classes' => $classes,
                'managers' => $managers,
            ]);
        } catch (Exception $e) {

            return view('error', ["message" => __('vocab.no_data') . $e->getMessage()]);
        }
    }

    //Show single enrollment
    public function show(Enrollment $enrollment)
    {
        return view('enrollements.show', [
            'enrollment' => $enrollment
        ]);
    }

    // Store enrollment Data
    public function store(Request $request, V1EnrollmentController $controller)
    {
        $response = $controller->store($request);
        // Get the HTTP status code from the response
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array
        if ($status_code === 201) {
            $message = isset($data['message']) ? $data['message'] : __('vocab.enrollment_successful');
            return redirect()->route('classes.show', ['classe' => $request->get('classeId')])->with('message', $message);
        } else {
            $message = isset($data['message']) ? $data['message'] : __('vocab.action_failure') . ' ' . __('vocab.try_again');
            return back()->withErrors(['message' => $message]);;
        }
    }

    // Accept  enrollment
    public function accept(Enrollment $enrollment, V1EnrollmentController $controller)
    {
        $response = $controller->accept($enrollment);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array


        if ($status_code === 200) {
            $message = isset($data['message']) ? $data['message'] : __('vocab.enrollment_successful');
        } else {
            $message = isset($data['message']) ? $data['message'] : __('vocab.action_failure') . ' ' . __('vocab.try_again');
        }
        return redirect()->route('enrollments.index')->with('message', $message);
    }

    // Accept  all enrollments
    public function acceptAll(Request $request, V1EnrollmentController $controller)
    {
        
        $response = $controller->acceptAll($request);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array


        if ($status_code === 200) {
            $message = isset($data['message']) ? $data['message'] : __('vocab.enrollment_successful');
        } else {
            $message = isset($data['message']) ? $data['message'] : __('vocab.action_failure') . ' ' . __('vocab.try_again');
        }
        return redirect()->route('enrollments.index')->with('message', $message);
    }

    // Delete  enrollment
    public function destroy(Enrollment $enrollment)
    {
        $controller = new V1EnrollmentController();
        $response = $controller->destroy($enrollment);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array


        if ($status_code === 200) {
            $message = isset($data['message']) ? $data['message'] :  __('vocab.delete_success');
        } else {
            $message = isset($data['message']) ? $data['message'] : __('vocab.action_failure') . ' ' . __('vocab.try_again');
        }
        return redirect()->route('enrollments.index')->with('message', $message);
    }

    //Destroy all enrollments
    public function destroyAll(Request $request, V1EnrollmentController $controller)
    {
        $response = $controller->destroyAll($request);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array


        if ($status_code === 200) {
            $message = isset($data['message']) ? $data['message'] : __('vocab.delete_success');
        } else {
            $message = isset($data['message']) ? $data['message'] : __('vocab.delete_failed');
        }
        return redirect()->route('enrollments.index')->with('message', $message);
    }
}
