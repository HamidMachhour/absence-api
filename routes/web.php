<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cookie;
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\AdminController;
use App\Http\Controllers\Web\AppelController;
use App\Http\Controllers\Web\OrderController;
use App\Http\Controllers\Web\ClasseController;
use App\Http\Controllers\Web\SeanceController;
use App\Http\Controllers\Web\AddressController;
use App\Http\Controllers\Web\StudentController;
use App\Http\Controllers\Web\UserImageController;
use App\Http\Controllers\Web\ClasseImageController;
use App\Http\Controllers\Web\StudentCandidateController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Common Resource Routes:
// index - Show all classes
// show - Show single classe
// create - Show form to create new classe
// store - Store new classe
// edit - Show form to edit classe
// update - Update classe
// destroy - Delete classe

/*
| Public routes
*/

Route::get('/error', function () {
    // This is your default route logic
    // You can modify this to match your requirements

    return view('error', ['message' => request()->get('message')]);
})->name('error.route');

Route::get('/dashboard', function () {

    if (Auth::check())
        return redirect()->route('users.dashboard',['user'=>auth()->user()]);
    else
        return redirect()->route('home.route');
})->name('dashboard.route');

Route::get('/', function () {

    if (!UserController::hasAdmin())
        return redirect()->route("admins.register");

    $token = Cookie::get('token');
    if (!$token) {
        auth()->logout();
    }

    return view('home');
})->name('home.route');


Route::group(['prefix' => 'users', 'middleware' => 'auth'], function () {
    Route::get('/', [UserController::class, 'index'])->name('users.index');
    Route::get('/{user}/show', [UserController::class, 'show'])->name('users.show');
    Route::post('/{user}/logout', [UserController::class, 'logout'])->name("users.logout");
    Route::delete('/{user}/destroy', [UserController::class, 'destroy'])->name("users.destroy");
    Route::put('/{user}/update', [UserController::class, 'update'])->name("users.update");
    Route::get('/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::get('/{user}/dashboard', [UserController::class, 'dashboard'])->name('users.dashboard');
});


Route::group(['prefix' => 'classes', 'middleware' => 'auth'], function () {
    // store New User
    Route::delete('/{classe}/destroy', [ClasseController::class, 'destroy'])->name("classes.destroy");
    Route::put('/{classe}/update', [ClasseController::class, 'update'])->name("classes.update");
    Route::get('/{classe}/edit', [ClasseController::class, 'edit'])->name('classes.edit');
    Route::post('/{classe}/default', [ClasseController::class, 'setDefault'])->name('classes.default');
    Route::get('/{classe}/importForm', [ClasseController::class, 'importForm'])->name('classes.importForm');
    Route::get('/{classe}/export', [ClasseController::class, 'export'])->name('classes.export');
    Route::post('/{classe}/import', [ClasseController::class, 'import'])->name('classes.import');
});

Route::group(['prefix' => 'students', 'middleware' => 'auth'], function () {
    Route::get('/manage/{classe}', [StudentController::class, 'manage'])->name("students.manage");
    Route::post('/store', [StudentController::class, 'store'])->name("students.store");
    Route::post('/{student}/copy', [StudentController::class, 'copy'])->name("students.copy");
    Route::delete('/{student}/destroy', [StudentController::class, 'destroy'])->name("students.destroy");
    Route::put('/{student}/update', [StudentController::class, 'update'])->name("students.update");
    Route::get('/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
});

Route::group(['prefix' => 'candidates', 'middleware' => 'auth'], function () {
    Route::get('/', [StudentCandidateController::class, 'index'])->name("candidates.index");
    Route::get('/{classe}', [StudentCandidateController::class, 'manage'])->name("candidates.manage");
    Route::post('/store', [StudentCandidateController::class, 'store'])->name("candidates.store");
    Route::get('/{student}/accept', [StudentCandidateController::class, 'accept'])->name("candidates.accept");
    Route::delete('/{student}/destroy', [StudentCandidateController::class, 'destroy'])->name("candidates.destroy");
});

Route::group(['prefix' => 'my-classes', 'middleware' => 'auth'], function () {
    Route::get('/', [ClasseController::class, 'manage'])->name("classes.manage");
});

Route::group(['prefix' => 'seances', 'middleware' => 'auth'], function () {
    Route::get('/manage', [SeanceController::class, 'manage'])->name("seances.manage");
    Route::get('/create', [SeanceController::class, 'create'])->name('seances.create');
    Route::post('/store', [SeanceController::class, 'store'])->name('seances.store');
    Route::delete('/{seance}/destroy', [SeanceController::class, 'destroy'])->name("seances.destroy");
    Route::put('/{seance}/update', [SeanceController::class, 'update'])->name("seances.update");
    Route::get('/{seance}/edit', [SeanceController::class, 'edit'])->name('seances.edit');

    Route::get('/{seance}/importForm', [SeanceController::class, 'importForm'])->name('seances.importForm');
    Route::get('/{seance}/export', [SeanceController::class, 'export'])->name('seances.export');
    Route::post('/{seance}/import', [SeanceController::class, 'import'])->name('seances.import');
});
Route::group(['prefix' => 'seances'], function () {
    Route::get('/', [SeanceController::class, 'index'])->name("seances.index");
    Route::get('/{seance}', [SeanceController::class, 'show'])->name('seances.show');
});

Route::group(['prefix' => 'appel', 'middleware' => 'auth'], function () {

    Route::post('/push_student', [AppelController::class, 'pushStudent'])->name("appels.pushStudent");
    Route::post('/push_all_student', [AppelController::class, 'pushAllStudents'])->name("appels.pushAllStudents");
    Route::get('/{appel}', [AppelController::class, 'show'])->name("appels.show");

    Route::get('/{seance}/checklist', [AppelController::class, 'checklist'])->name("seances.checklist");

    Route::post('/{appel}/submit', [AppelController::class, 'submit'])->name("appels.submit");
    Route::post('/{appel}/clear', [AppelController::class, 'clear'])->name("appels.clear");
    Route::get('/{appel}/appel_to_absence', [AppelController::class, 'appel_to_absence'])->name("appels.appel_to_absence");
    Route::post('/{appel}/place_absence', [AppelController::class, 'place_absence'])->name("appels.place_absence");
    Route::post('/{appel}/popStudent/{appel_student}', [AppelController::class, 'popStudent'])->name("appels.popStudent");
    Route::post('/{appel}/savePlage/{appel_student}', [AppelController::class, 'savePlage'])->name("appels.savePlage");
    Route::post('/{appel}/saveJustification/{appel_student}', [AppelController::class, 'saveJustification'])->name("appels.saveJustification");
});

Route::group(['prefix' => 'my-absences', 'middleware' => 'auth'], function () {
    Route::get('/', [OrderController::class, 'index'])->name("absences.index");
    Route::get('/{absence}', [OrderController::class, 'show'])->name("absences.show");
    Route::post('/{absence}/cancel', [OrderController::class, 'cancel'])->name("absences.cancel");
    Route::get('/{absence}/payment', [OrderController::class, 'payment'])->name("absences.payment");
});

Route::group(['prefix' => 'my-addresses', 'middleware' => 'auth'], function () {
    Route::get('/', [AddressController::class, 'index'])->name("addresses.index");
    Route::get('/create', [AddressController::class, 'create'])->name("addresses.create");
    Route::post('/store', [AddressController::class, 'store'])->name("addresses.store");

    Route::get('/{address}', [AddressController::class, 'show'])->name("addresses.show");
    Route::get('/{address}/edit', [AddressController::class, 'edit'])->name("addresses.edit");
    Route::post('/{address}/default', [AddressController::class, 'setDefault'])->name('addresses.default');
    Route::put('/{address}/update', [AddressController::class, 'update'])->name("addresses.update");

    Route::delete('/{address}/destroy', [AddressController::class, 'destroy'])->name("addresses.destroy");
});

Route::group(['prefix' => 'classe-images', 'middleware' => 'auth'], function () {

    Route::post('/store', [ClasseImageController::class, 'store'])->name('classeImages.store');

    Route::delete('/{image}/destroy', [ClasseImageController::class, 'destroy'])->name('classeImages.destroy');
    Route::get('/{classe}/create', [ClasseImageController::class, 'create'])->name('classeImages.create');
    Route::post('/{classe}/setDefault/{image}', [ClasseImageController::class, 'setDefault'])->name('classeImages.setDefault');
});

Route::group(['prefix' => 'user-images', 'middleware' => 'auth'], function () {

    Route::post('/store', [UserImageController::class, 'store'])->name('userImages.store');

    Route::delete('/{image}/destroy', [UserImageController::class, 'destroy'])->name('userImages.destroy');
    Route::get('/{user}/create', [UserImageController::class, 'create'])->name('userImages.create');
    Route::post('/{user}/setDefault/{image}', [UserImageController::class, 'setDefault'])->name('userImages.setDefault');
});

Route::group(['prefix' => 'admin', 'middleware' => 'adminauth'], function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name("admins.dashboard");
});
// Routes accessible to guest users
Route::group(['prefix' => 'classes'], function () {
    Route::get('/', [ClasseController::class, 'index'])->name("classes.index");
    Route::get('/{classe}', [ClasseController::class, 'show'])->name('classes.show');
});

Route::group(['prefix' => 'classe-images'], function () {
    Route::get('/{classe}', [ClasseImageController::class, 'index'])->name('classeImages.index');
});


Route::group(['prefix' => 'user-images'], function () {
    Route::get('/{user}', [UserImageController::class, 'show'])->name('userImages.show');
});

Route::group(['prefix' => 'classe'], function () {
    // Show Create Form
    Route::get('/create', [ClasseController::class, 'create'])->name('classes.create');
    Route::post('/store', [ClasseController::class, 'store'])->name("classes.store");
});

Route::group(['prefix' => 'students'], function () {
    Route::get('/', [StudentController::class, 'index'])->name("students.index");
    Route::get('/{student}', [StudentController::class, 'show'])->name('students.show');
});


Route::group(['prefix' => 'user'], function () {
    // Show Register/Create Form
    Route::get('/register', [UserController::class, 'register'])->name('users.register');

    // store New User
    Route::post('/store', [UserController::class, 'store'])->name("users.store");

    // Show Login Form
    Route::get('/login', [UserController::class, 'login'])->name('users.login');

    // Log In User
    Route::post('/authenticate', [UserController::class, 'authenticate'])->name('users.authenticate');
});

Route::group(['prefix' => 'admin'], function () {
    // Show Register/Create Form
    Route::get('/register', [AdminController::class, 'register'])->name('admins.register');

    // store New Admin
    Route::post('/store', [AdminController::class, 'store'])->name("admins.store");
});
