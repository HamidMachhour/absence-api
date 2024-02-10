<?php

namespace App\Http\Controllers\Web;

use Exception;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use App\Http\Controllers\Api\V1\UserController as V1UserController;
use App\Models\Role;

class UserController extends Controller
{

    public static function hasAdmin()
    {
        $admins = User::withRole('admin')->get();
        return ($admins && count($admins) > 0);
    }

    public function dashboard()
    {
        if (Auth::check() && Auth::user()->hasRole('admin'))
            return redirect()->route('admins.dashboard');

        return view('users.dashboard');
    }

    // Show Register/Create Form
    public function register()
    {
        if (Auth::check()) {
            return redirect()->route('users.dashboard')->with('message', 'سجلت الدخول بنجاح');
        }
        return view('users.register');
    }

    // Create New User
    public function store(Request $request, $role = '')
    {
        $controller = new V1UserController();
        $response = $controller->store($request, $role);
        // Get the HTTP status code from the response
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array
        // Check the status code and perform actions accordingly
        if ($status_code === 201) {
            $message = isset($data['message']) ? $data['message'] : '.نجاح التسجيل';
            // Registration success (HTTP 201 Created)
            return redirect()->route('users.login')->with('message', $message);
        } else {

            $message = isset($data['message']) ? $data['message'] : '.فشل التسجيل';
            // echo $message;
            return back()->withErrors(['message' => $message]);
        }
    }

    // Logout User
    public function logout(Request $request, User $user)
    {
        if (Auth::user()->id !== $user->id) {
            return back()->withErrors(['message' => 'تم الرفض، لا تمتلك التفويض الكافي للقيام بالعملية']);
        }

        $token = Cookie::get('token');
        // Check if the token is present
        if (!$token) {
            // The user is not authenticated
            return redirect()->route('home.route');
        }

        $controller = new V1UserController();
        $request['token'] = $token;
        $response = $controller->logout($request, $user);
        // Get the HTTP status code from the response
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array
        if ($status_code === 201) {
            $message = isset($data['message']) ? $data['message'] : 'تم الخروج بنجاح!';

            // Delete the token cookie
            Cookie::forget('token');

            return redirect()->route('home.route')->with('message', $message);
        } else {
            $message = isset($data['message']) ? $data['message'] : 'فشل الخروج من النظام!';
            return back()->with(['message' => $message]);
        }
    }

    // Show Login Form
    public function login()
    {
        if (Auth::check()) {
            return redirect()->route('home.route')->with('message', 'سجلت الدخول بنجاح');
        }
        return view('users.login');
    }

    // Authenticate User
    public function authenticate(Request $request)
    {
        $controller = new V1UserController();
        $response = $controller->login($request);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array
        // Check the status code and perform actions accordingly
        if ((isset($data['token'])) && $status_code === 200 || $status_code === 201) {
            // Authentication successful
            $message = isset($data['message']) ? $data['message'] : 'سجلت الدخول بنجاح!';
            $token = $data['token'];

            // Store the token in a cookie
            Cookie::queue('token', $token, 60 * 60 * 24); // 24 hours

            // die(auth()->user()->hasRole('admin'));

            if (auth()->user()->hasRole('admin'))
                return redirect()->route('admins.dashboard')->with('message', $message);

            return redirect()->route('users.dashboard', ['user' => auth()->user()])->with('message', $message);
        } else {
            $message = isset($data['message']) ? $data['message'] : ' لم يتم الخول بنجاح';
            return redirect()->route('users.login')->with('message', $message);
        }
    }

    public function index(Request $request)
    {
        try {
            if (!Auth::check())
                return redirect()->route('users.login');

            $v1Controller = new V1UserController();
            $response = $v1Controller->index($request);

            if (method_exists($response, 'status') && $response->status() == 404)
                return view('error', ["message" => ' غير متوفر']);

            $params = $this->prepareFilterParams($request, $response);

            return view('users.index', [
                'users' => $response,
                'params' => $params
            ]);
        } catch (Exception $e) {
            return view('error', ["message" => 'غير متوفر' . $e->getMessage()]);
        }
    }
    public function prepareFilterParams(Request $request, $response)
    {

        $params = [];
        if (!$response || count($response) == 0)
            return $params;

        if ($request->filled('role_id')) {
            $params['role_id'] = 'الدور:' . $request->role_name;
        }
        if ($request->filled('verified')) {
            $params['verified'] = 'حساب ' . ($request->verified === 'yes' ? 'محقق' : 'غير محقق');
        }

        if ($request->filled('search')) {
            $params['search'] = 'بحث ' . $request->search;
        }

        return $params;
    }
    //Show single user
    public function show(User $user)
    {
        //dd($user);
        return view('users.show', [
            'user' => $user
        ]);
    }


    // Show Edit Form
    public function edit(User $user)
    {
        if (!Auth::user()->hasRole('admin') && Auth::user()->id !== $user->id) {
            return redirect()->route('home.route')->with('message',  'غير مسموح');
        }

        return view('users.edit', ['user' => $user]);
    }

    // Update user Data
    public function update(Request $request, User $user)
    {
        if (!Auth::user()->hasRole('admin') && Auth::user()->id !== $user->id) {
            return redirect()->route('home.route')->with('message',  'غير مسموح');
        }

        $token = Cookie::get('token');

        if (!$token) {
            // The user is not authenticated
            return redirect()->route('home.route');
        }
        $controller = new V1UserController();
        $response = $controller->update($request, $user);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array


        if ($status_code === 200) {
            // update successful
            $message = isset($data['message']) ? $data['message'] : 'تم التعديل بنجاح';
            //get token and save
            return redirect()->route('users.show', ['user' => $user])->with('message', $message);
        } elseif ($status_code === 201) {
            return $this->logout($request, $user);
        } else {
            $message = isset($data['message']) ? $data['message'] : 'لم يتم التعديل بنجاح';
            return back()->withErrors('message', $message);
        }
    }

    // Delete user
    public function destroy(User $user)
    {
        if (!Auth::user()->hasRole('admin')) {
            return redirect()->route('home.route')->with('message',  'غير مسموح');
        }
        $controller = new V1UserController();
        $response = $controller->destroy($user);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array

        if ($status_code === 200) {
            // destroy successful
            $message = isset($data['message']) ? $data['message'] : 'Profile deleted successfully';

            Cookie::forget("token");
            //get token and save
            return redirect()->route('home.route')->with('message', $message);
        } else {
            $message = isset($data['message']) ? $data['message'] : 'delete failed.';
            return back()->with('message', $message);
        }
    }
}
