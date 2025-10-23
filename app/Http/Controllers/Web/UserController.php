<?php

namespace App\Http\Controllers\Web;

use Exception;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use App\Http\Controllers\Api\V1\UserController as V1UserController;

class UserController extends Controller
{

    public static function hasAdmin()
    {
        $admins = User::withRole('admin')->get();
        return ($admins && count($admins) > 0);
    }

    public function dashboard(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('home')->with(['message',  __('vocab.not_authorized')]);
        }
        $role_name = Auth::user()->role();
        switch ($role_name) {
            case 'admin':
                return redirect()->route('admins.dashboard');
                break;
            case 'manager':

                $myLatestAbsences = User::myLatestAbsencesCount();
                $latestAbsences = User::latestAbsencesCount($request);

                return view('users.dashboard.manager', [
                    'latestAbsences' => $latestAbsences,
                    'myLatestAbsences' => $myLatestAbsences
                ]);

            case 'teacher':
                return view('users.dashboard.teacher');
                break;
            case 'student':
                return view('users.dashboard.student');
                break;
            case 'guest':
                return view('users.dashboard.guest');
                break;
            default:
                return redirect()->route('home');
        }
    }

    // Show Register/Create Form
    public function register()
    {
        if (Auth::check()) {
            return redirect()->route('users.dashboard')->with('message', __('vocab.register_success'));
        }
        return view('users.register');
    }

    // Create New User
    public function store(Request $request, V1UserController $controller, $role = '')
    {
        $response = $controller->store($request, $role);
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

    // Logout user

    public function logout(Request $request, V1UserController $controller)
    {
        if (!Auth::check()) {
            return redirect()->route('users.login');
        }

        // Prepare data for the API call (e.g., to indicate logout all devices)
        $requestData = [
            'logout_all_devices' => $request->input('logout_all_devices'),
        ];

        // Call the API Controller's logout function
        $response = $controller->logout(new Request($requestData));

        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true);

        if ($status_code === 201) {
            $message = isset($data['message']) ? $data['message'] : __('vocab.logout_success');

            // Delete the token cookie and redirect
            return redirect()
                ->route('users.login')
                ->with('message', $message)
                ->withCookie(Cookie::forget('token'));
        } else {
            $message = isset($data['message']) ? $data['message'] : __('vocab.logout_failure');
            return back()->with('error', $message);
        }
    }
    // Show Login Form
    public function login()
    {
        if (Auth::check()) {
            return redirect()->route('home')->with('message', __('vocab.login_success'));
        }
        return view('users.login');
    }

    // Authenticate user and create session + cookie

    public function authenticate(Request $request, V1UserController $userController)
    {
        // 1. API verifies credentials, creates token, and returns user data.
        $response = $userController->login($request);
        $data = $response->getOriginalContent();

        if ($response->getStatusCode() === 200) {
            // 2. The API response already contains the authenticated user object
            $user = $data['user'];

            // 3. CRITICAL: Use the returned user object to establish the WEB session
            Auth::guard('web')->login($user);
            $request->session()->regenerate();

            $token = $data['token'];
            $message = isset($data['message']) ? $data['message'] : __('vocab.login_success');
            // 4. Store the API token in a cookie
            Cookie::queue('token', $token, 1440); // 24 hours in minutes

            // 5. Redirect the now-authenticated user
            if ($user->hasRole('admin')) {
                return redirect()->route('admins.dashboard')->with('message', $message);
            }

            // Use ->intended() or the hardcoded route, but ensure the dashboard route does NOT require a {user} parameter.
            return redirect()->route('users.dashboard')->with('message', $message); // Use the fixed route name

        }

        // Authentication failed (API returned non-200 or no token)
        $message = $data['message'] ?? __('vocab.login_failure');
        $validationErrors = $data['errors'] ?? [];

        return redirect()->route('users.login')
            ->with('message', $message)
            ->withErrors($validationErrors);
    }

    public function index(Request $request, V1UserController $v1Controller)
    {
        try {
            if (!Auth::check())
                return redirect()->route('users.login');

            $response = $v1Controller->index($request);


            if ($response instanceof JsonResponse) {
                $status = $response->getStatusCode();
                $data = $response->getData(); // Get the underlying data object from the JSON response

                // Test on response status for errors
                if ($status === 401 || $status === 404) {
                    $message = $data->message ?? __('vocab.not_authorized');
                    return redirect()->route('error', ['message' => $message]);
                }
            }
            $params = $this->prepareFilterParams($request, $response);

            return view('users.index', [
                'users' => $response,
                'params' => $params
            ]);
        } catch (Exception $e) {
            return view('error', ["message" => __('vocab.empty')  . $e->getMessage()]);
        }
    }
    public function prepareFilterParams(Request $request, $response)
    {

        $params = [];
        if (!$response || count($response) == 0)
            return $params;

        if ($request->filled('roleId')) {
            $params['roleId'] = __('vocab.role') . $request->role_name;
        }
        if ($request->filled('verified')) {
            $params['verified'] = __('vocab.account') . ' ' . ($request->verified === 'yes' ? __('vocab.verified') : __('vocab.not_verified'));
        }

        if ($request->filled('search')) {
            $params['search'] = __('vocab.search') . ' ' . $request->search;
        }

        return $params;
    }
    //Show single user
    public function show(User $user)
    {
        return view('users.show', [
            'user' => $user
        ]);
    }


    // Show Edit Form
    public function edit(User $user)
    {
        if (!Auth::user()->hasRole('admin') && Auth::user()->id !== $user->id) {
            return redirect()->route('home')->with('message',  __('vocab.not_authorized'));
        }

        return view('users.edit', ['user' => $user]);
    }


    public function update(Request $request, User $user, V1UserController $controller)
    {
        // Check for authorization: Admin or updating self
        if (!Auth::user()->hasRole('admin') && Auth::user()->id !== $user->id) {
            return redirect()->route('home')->with('message',  __('vocab.not_authorized'));
        }

        $token = Cookie::get('token');

        if (!$token) {
            // The user is not authenticated (or token expired/missing)
            return redirect()->route('login'); // Redirect to login, not home, if token is missing
        }

        // Call the API/Business Logic Controller's update function
        $response = $controller->update($request, $user);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array

        // Determine if the current user is updating their own account
        $isUpdatingSelf = Auth::user()->id === $user->id;

        if ($status_code === 200) {
            // Standard successful update (no password change)
            $message = isset($data['message']) ? $data['message'] : __('vocab.update_success');

            return redirect()->route('users.edit', ['user' => $user])->with('message', $message);
        } elseif ($status_code === 201) {
            // Password change successful (201 status code from the API controller)

            $message = isset($data['message']) ? $data['message'] : __('vocab.update_success');

            // --- FIX: Conditional Logout Logic ---
            if ($isUpdatingSelf) {
                // If the current user changed their own password, force logout
                return redirect()->route('users.logout', ['user' => $user])->with('message', $message);
            } else {
                // If an admin changed another user's password, stay logged in and redirect to the edit page
                return redirect()->route('users.edit', ['user' => $user])->with('message', $message);
            }
            // ------------------------------------

        } else {
            // Handle other status codes (e.g., 422 Validation Error from API controller)

            // It's generally better to pass back the API errors for better user experience
            $message = isset($data['message']) ? $data['message'] : __('vocab.update_failure');

            // Note: For validation errors, you might want to return the actual errors array
            // return back()->withErrors($data['errors'] ?? ['general' => $message]);
            return back()->with('error', $message); // Using 'error' for failure messages
        }
    }
    // Activate user
    public function activate(User $user, V1UserController $controller)
    {

        if (!Auth::user()->hasRole('admin')) {
            return redirect()->route('home')->with('message',  __('vocab.not_authorized'));
        }

        $response = $controller->activate($user);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array 
        if ($status_code === 200) {
            // activate successful
            $message = isset($data['message']) ? $data['message'] : __('vocab.activation_success');
            //get token and save
            return back()->with('message', $message);
        } else {
            $message = isset($data['message']) ? $data['message'] : __('vocab.activation_failure');
            return back()->withErrors('message', $message);
        }
    }

    // Refute any user except admin it self
    public function refute(User $user, V1UserController $controller)
    {
        if (!Auth::user()->hasRole('admin') || Auth::user()->id === $user->id) {
            return redirect()->route('home')->with('message',  __('vocab.not_authorized'));
        }
        $response = $controller->refute($user);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array 
        if ($status_code === 200) {
            // refute successful
            $message = isset($data['message']) ? $data['message'] : __('vocab.refute_success');
            //get token and save
            return back()->with('message', $message);
        } else {
            $message = isset($data['message']) ? $data['message'] : __('vocab.refute_failure');
            return back()->withErrors('message', $message);
        }
    }

    // Delete user
    public function destroy(User $user, V1UserController $controller)
    {
        if (!Auth::user()->hasRole('admin')) {
            return redirect()->route('home')->with('message',  __('vocab.not_authorized'));
        }
        $response = $controller->destroy($user);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array

        if ($status_code === 200) {
            // destroy successful
            $message = isset($data['message']) ? $data['message'] : __('vocab.delete_success');
            // Delete the token cookie
            Cookie::forget("token");
            return back()->with('message', $message);
        } else {
            $message = isset($data['message']) ? $data['message'] : __('vocab.delete_failure');
            return back()->withErrors('message', $message);
        }
    }
}
