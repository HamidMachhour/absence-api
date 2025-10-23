<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\V1\UserController as V1UserController;
use App\Models\AbsenceStudent;
use App\Models\User;

class AdminController extends Controller
{




    public function dashboard(Request $request)
    {

        if (Auth::check() && Auth::user()->hasRole('admin')) {

            $latestAbsences = User::latestAbsencesCount($request);
            $myLatestAbsences = User::myLatestAbsencesCount();

            return view('users.dashboard.admin', [
                'latestAbsences' => $latestAbsences,
                'myLatestAbsences' => $myLatestAbsences
            ]);
        } else
            return redirect()->route('home')->with(['message',  __('vocab.not_authorized')]);
    }

    public function register()
    {
        $message = __('vocab.already_logged_in');
        if (Auth::check()) {
            return redirect()->route('error', ['message' => $message])->with('message', $message);
        }
        return view('users.register', ['role' => 'admin']);
    }

    protected function store(Request $request, V1UserController $controller)
    {

        $response = $controller->store($request, 'admin');
        // Get the HTTP status code from the response
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array
        // Check the status code and perform actions accordingly
        if ($status_code === 201) {
            $message = isset($data['message']) ? $data['message'] : __('vocab.register_success');
            // Registration success (HTTP 201 Created)
            return redirect()->route('users.login')->with('message', $message);
        } else {

            $message = isset($data['message']) ? $data['message'] : __('vocab.register_failure');
            // echo $message;
            return back()->withErrors(['message' => $message]);
        }
    }

    public function usersImportForm()
    {
        if (!Auth::check() || !Auth::user()->hasRole('admin')) {
            $message = __('vocab.not_authorized');
            return redirect()->route('error', ['message' => $message])->with('message', $message);
        }

        return view('users.import_form');
    }

    public function usersExportForm()
    {
        if (!Auth::check() || !Auth::user()->hasRole('admin')) {
            $message = __('vocab.not_authorized');
            return redirect()->route('error', ['message' => $message])->with('message', $message);
        }
        return view('users.export_form');
    }

    public function usersImport(Request $request, V1UserController $controller)
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
            $message = isset($data['message']) ? $data['message'] : __('vocab.import_success');
        } else {
            $message = isset($data['message']) ? $data['message'] : __('vocab.import_failure');
        }

        return redirect()->route('users.index')->with('message', $message);
    }

    public function usersExport(Request $request, V1UserController $controller)
    {
        $response = $controller->export($request);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array

        if ($status_code === 200) {
            return $response;
        }

        $message = isset($data['message']) ? $data['message'] : __('vocab.export_failure');

        return redirect()->route('users.index')->with('message', $message);
    }
}
