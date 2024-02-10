<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class AdminController extends Controller
{

    public function dashboard()
    {

        if (Auth::check() && Auth::user()->hasRole('admin'))
            return view('admins.dashboard');
        else
            return redirect()->route('home.route')->with(['message' ,  'غير مسموح']);
    }

    public function register()
    {
        $message='لقد قمت بتسجيل الدخول بالفعل، قم بتسجيل الخروج وإنشاء حساب لمدير الموقع!';
        if (Auth::check()) {
            return redirect()->route('error.route',['message'=>$message])->with('message', $message);
        }
        return view('users.register', ['role' => 'admin']);
    }

    protected function store(Request $request)
    {
        $controller = new UserController();
        return $controller->store($request, 'admin');
    }
}
