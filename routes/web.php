<?php
/*
|--------------------------------------------------------------------------
| Laravel Imports Section
|--------------------------------------------------------------------------
|
| Here, we import all necessary classes from Laravel framework and our app's
| controllers. These imports enable us to use Laravel's core functionality
| and our custom controller methods throughout the routes file.
*/

use App\Http\Controllers\Web\UserRoleController;
use App\Http\Controllers\Web\RoleController;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cookie;
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\AdminController;
use App\Http\Controllers\Web\AttendanceController;
use App\Http\Controllers\Web\AbsenceController;
use App\Http\Controllers\Web\ClasseController;
use App\Http\Controllers\Web\SeanceController;
use App\Http\Controllers\Web\AddressController;
use App\Http\Controllers\Web\StudentController;
use App\Http\Controllers\Web\UserImageController;
use App\Http\Controllers\Web\ClasseImageController;
use App\Http\Controllers\Web\EnrollmentController;
use App\Http\Controllers\Web\SubjectController;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Web\MessageController;
use App\Http\Controllers\Web\FriendshipController;
use App\Http\Controllers\Web\SchoolConfigurationController;

use App\Http\Controllers\Web\MisconductActController;
use App\Http\Controllers\Web\ProposedActionController;
use App\Http\Controllers\Web\MisconductReportController;
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

Route::get('/login', function () {
    // This is your default route logic
    // You can modify this to match your requirements

    return redirect()->route('users.login');
})->name('login');

Route::get('/error', function () {
    // This is your default route logic
    // You can modify this to match your requirements

    return view('error', ['message' => request()->get('message')]);
})->name('error');


Route::get('/dashboard', function () {

    if (Auth::check())
        return redirect()->route('users.dashboard', ['user' => auth()->user()]);
    else
        return redirect()->route('home');
})->name('dashboard');

Route::get('/', function () {

    if (!UserController::hasAdmin())
        return redirect()->route("admins.register");

    $token = Cookie::get('token');
    if (!$token) {

        auth()->logout();
    }

    return view('home');
})->name('home');


Route::group(['prefix' => 'messages', 'middleware' => 'auth'], function () {
    // Public routes for user registration and authentication
    Route::get('/contactForm', [MessageController::class, 'showContactForm'])->name("messages.contactForm");
    Route::post('/contact-us', [MessageController::class, 'contactUs'])->name("messages.contactUs");
    Route::get('/inbox', [MessageController::class, 'inbox'])->name("messages.inbox");
    Route::post('/sent', [MessageController::class, 'sent'])->name("messages.sent");
    Route::get('/reply', [MessageController::class, 'reply'])->name("messages.reply");

    // mark as read
    Route::post('/{message}/markAsRead', [MessageController::class, 'markAsRead'])->name("messages.markAsRead");
    Route::get('/{message}/show', [MessageController::class, 'show'])->name("messages.show");
    Route::get('/{to}/compose', [MessageController::class, 'compose'])->name("messages.compose");

    Route::delete('/{message}/destroy', [MessageController::class, 'destroy'])->name("messages.destroy");
});

Route::group(['prefix' => 'friends', 'middleware' => 'auth'], function () {
    // Public routes for user registration and authentication
    Route::get('/', [FriendshipController::class, 'friends'])->name("friends.index");
    Route::post('/store', [FriendshipController::class, 'addFriend'])->name("friends.store");
    Route::post('/{friend}/accept', [FriendshipController::class, 'acceptFriend'])->name("friends.accept");
    Route::post('/{friend}/block', [FriendshipController::class, 'blockFriend'])->name("friends.block");
    Route::delete('/{friend}/destroy', [FriendshipController::class, 'destroy'])->name("friends.destroy");
    Route::post('/{friend}/reject', [FriendshipController::class, 'rejectFriend'])->name("friends.reject");
});

// 1. Unauthenticated Routes (Login/Register)
// Use 'users' prefix for consistency with the protected group
Route::group(['prefix' => 'users'], function () {
    // Show Register/Create Form
    Route::get('/register', [UserController::class, 'register'])->name('users.register');

    // Store New User
    Route::post('/store', [UserController::class, 'store'])->name("users.store");

    // Show Login Form
    Route::get('/login', [UserController::class, 'login'])->name('users.login');

    // Log In User
    Route::post('/authenticate', [UserController::class, 'authenticate'])->name('users.authenticate');
});

// ------------------------------------------------------------------------------------------------

// 2. Authenticated Routes (Dashboard, Profile, Management)
Route::group(['prefix' => 'users', 'middleware' => 'auth'], function () {

    // Global Logout for the current user (Does not require a {user} parameter)
    Route::post('/logout', [UserController::class, 'logout'])->name("users.logout");

    // Main Dashboard/Profile for the current user (Does not require a {user} parameter)
    // The current user is known via Auth::user()
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('users.dashboard');

    // Index/List of all users (e.g., for admins)
    Route::get('/', [UserController::class, 'index'])->name('users.index');

    // ----------------------------------------------------------------------------------

    // General wildcard routes (User Management/Profile Edit for a specific user)
    // These routes must come last to avoid conflicts with static routes like /logout and /dashboard.

    Route::get('/{user}/show', [UserController::class, 'show'])->name('users.show');
    Route::get('/{user}/edit', [UserController::class, 'edit'])->name('users.edit');

    // Action routes
    Route::put('/{user}/update', [UserController::class, 'update'])->name("users.update");
    Route::put('/{user}/activate', [UserController::class, 'activate'])->name('users.activate');
    Route::put('/{user}/refute', [UserController::class, 'refute'])->name('users.refute');
    Route::delete('/{user}/destroy', [UserController::class, 'destroy'])->name("users.destroy");
});

Route::group(['prefix' => 'classes', 'middleware' => 'auth'], function () {
    // 1. Most specific static routes (no wildcards)
    Route::get('/export', [ClasseController::class, 'exportClasses'])->name('export.classes'); // Export classes as CSV
    Route::get('/importForm', [ClasseController::class, 'importClassesForm'])->name('importForm.classes'); // Open import form
    Route::post('/import', [ClasseController::class, 'importClasses'])->name('import.classes'); // Handle CSV import
    Route::delete('/destroyAll', [ClasseController::class, 'destroyAll'])->name("classes.destroyAll");

    // 2. More specific routes with a static segment followed by a wildcard
    Route::get('/{classe}/importForm', [ClasseController::class, 'importForm'])->name('classes.importForm');
    Route::get('/{classe}/export', [ClasseController::class, 'export'])->name('classes.export');
    Route::post('/{classe}/import', [ClasseController::class, 'import'])->name('classes.import');
    Route::get('/{classe}/printCards', [ClasseController::class, 'printCards'])->name('classes.printCards');

    // 3. General CRUD wildcard routes last
    Route::delete('/{classe}/destroy', [ClasseController::class, 'destroy'])->name("classes.destroy");
    Route::put('/{classe}/update', [ClasseController::class, 'update'])->name("classes.update");
    Route::get('/{classe}/edit', [ClasseController::class, 'edit'])->name('classes.edit');
    Route::post('/{classe}/default', [ClasseController::class, 'setDefault'])->name('classes.default');
});

Route::group(['prefix' => 'students', 'middleware' => 'auth'], function () {
    // 1. Most specific routes (no wildcards)
    Route::post('/store', [StudentController::class, 'store'])->name("students.store");

    // 2. Routes with specific static segments and wildcards
    Route::post('/{student}/copy', [StudentController::class, 'copy'])->name("students.copy");
    Route::get('/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
    Route::get('/{student}/card', action: [StudentController::class, 'card'])->name('students.card');
    Route::get('/{student}/printCard', [StudentController::class, 'printCard'])->name('students.printCard');

    // 3. General routes with wildcards
    Route::get('/manage/{classe}', [StudentController::class, 'manage'])->name("students.manage");
    Route::get('/cards/{classe}', [StudentController::class, 'cards'])->name("students.cards");
    Route::delete('/{student}/destroy', [StudentController::class, 'destroy'])->name("students.destroy");
    Route::put('/{student}/update', [StudentController::class, 'update'])->name("students.update");
});

Route::group(['prefix' => 'enrollments', 'middleware' => 'auth'], function () {
    Route::get('/', [EnrollmentController::class, 'index'])->name("enrollments.index");

    // Corrected Order: Place the specific route above the general one
    Route::get('/acceptAll', [EnrollmentController::class, 'acceptAll'])->name("enrollments.acceptAll");
    Route::get('/{enrollment}/accept', [EnrollmentController::class, 'accept'])->name("enrollments.accept");

    Route::post('/store', [EnrollmentController::class, 'store'])->name("enrollments.store");

    // Same principle for the destroy routes
    Route::delete('/destroyAll', [EnrollmentController::class, 'destroyAll'])->name("enrollments.destroyAll");
    Route::delete('/{enrollment}/destroy', [EnrollmentController::class, 'destroy'])->name("enrollments.destroy");
});

Route::group(['prefix' => 'my-classes', 'middleware' => 'auth'], function () {
    Route::get('/', [ClasseController::class, 'manage'])->name("classes.manage");
});


Route::group(['prefix' => 'seances', 'middleware' => 'auth'], function () {
    // Specific static routes first
    Route::get('/manage', [SeanceController::class, 'manage'])->name("seances.manage");
    Route::get('/create', [SeanceController::class, 'create'])->name('seances.create');
    Route::get('/importForm', [SeanceController::class, 'importForm'])->name('seances.importForm');
    Route::get('/export', [SeanceController::class, 'export'])->name('seances.export');

    // POST/PUT/DELETE routes (can be mixed with GET, but logical grouping is helpful)
    Route::post('/store', [SeanceController::class, 'store'])->name('seances.store');
    Route::post('/import', [SeanceController::class, 'import'])->name('seances.import');

    // Specific delete route
    Route::delete('/destroyAll', [SeanceController::class, 'destroyAll'])->name("seances.destroyAll");

    // General wildcard routes last
    Route::get('/{seance}/edit', [SeanceController::class, 'edit'])->name('seances.edit');
    Route::put('/{seance}/update', [SeanceController::class, 'update'])->name("seances.update");
    Route::delete('/{seance}/destroy', [SeanceController::class, 'destroy'])->name("seances.destroy");
});

Route::group(['prefix' => 'seances'], function () {
    // Public routes for viewing seances (accessible to all users)
    Route::get('/', [SeanceController::class, 'index'])->name("seances.index");
    Route::get('/{seance}', [SeanceController::class, 'show'])->name('seances.show');
});

Route::group(['prefix' => 'attendance', 'middleware' => 'auth'], function () {
    // 1. Most specific static routes (no wildcards)
    // In your web.php
    Route::post('/popAttendanceStudent/{attendance_student}', [AttendanceController::class, 'popAttendanceStudent'])->name("attendances.popAttendanceStudent");
    Route::post('/savePlage/{attendance_student}', [AttendanceController::class, 'savePlage'])->name("attendances.savePlage");

    // 2. Specific routes with a static segment and a wildcard
    Route::get('/{seance}/checklist', [AttendanceController::class, 'checklist'])->name("attendances.checklist");

    Route::get('/{attendance}/students', [AttendanceController::class, 'refreshAttendance'])->name('attendances.students.list');

    Route::get('/{attendance}/attendance_to_absence', [AttendanceController::class, 'attendanceToAbsence'])->name("attendances.attendance_to_absence");

    Route::get('/{attendance}/push_all_student', [AttendanceController::class, 'pushAllStudents'])->name("attendances.pushAllStudents");
    Route::post('/{attendance}/push_student', [AttendanceController::class, 'pushStudent'])->name("attendances.pushStudent");

    Route::get('/{attendance}/clear', [AttendanceController::class, 'clear'])->name("attendances.clear");



    Route::get('/{attendance}/qrcodecheck', [AttendanceController::class, 'qrcodeCheck'])->name("attendances.qrcodeCheck");
    Route::post('/{attendance}/submit', [AttendanceController::class, 'submit'])->name("attendances.submit");

    Route::post('/{attendance}/place_absence', [AttendanceController::class, 'place_absence'])->name("attendances.place_absence");

    Route::get('/{attendance}/popStudent/{scannedData}', [AttendanceController::class, 'popStudent'])->name("attendances.popStudent");


    // 3. General wildcard routes last
    Route::get('/{attendance}', [AttendanceController::class, 'show'])->name("attendances.show");
});


Route::group(['prefix' => 'absences', 'middleware' => 'auth'], function () {
    // 1. Most specific routes
    Route::get('/', [AbsenceController::class, 'index'])->name("absences.index");
    Route::get('/ofUser/{user}', [AbsenceController::class, 'ofUser'])->name("absences.ofUser");

    // 2. Wildcard routes with static segments
    Route::post('/{absence}/popStudent/{absence_student}', [AbsenceController::class, 'popStudent'])->name("absences.popStudent");
    Route::post('/{absence}/saveJustification/{absence_student}', [AbsenceController::class, 'saveJustification'])->name("absences.saveJustification");
    Route::delete('/{absence}/destroy', [AbsenceController::class, 'destroy'])->name("absences.destroy");

    // 3. General wildcard route last
    Route::get('/{absence}', [AbsenceController::class, 'show'])->name("absences.show");
});

Route::group(['prefix' => 'my-addresses', 'middleware' => 'auth'], function () {
    // 1. Static routes first
    Route::get('/', [AddressController::class, 'index'])->name("addresses.index");
    Route::get('/create', [AddressController::class, 'create'])->name("addresses.create");
    Route::post('/store', [AddressController::class, 'store'])->name("addresses.store");

    // 2. Wildcard routes with static segments
    Route::get('/{address}/edit', [AddressController::class, 'edit'])->name("addresses.edit");
    Route::post('/{address}/default', [AddressController::class, 'setDefault'])->name('addresses.default');
    Route::put('/{address}/update', [AddressController::class, 'update'])->name("addresses.update");
    Route::delete('/{address}/destroy', [AddressController::class, 'destroy'])->name("addresses.destroy");

    // 3. General wildcard route last
    Route::get('/{address}', [AddressController::class, 'show'])->name("addresses.show");
});

Route::group(['prefix' => 'classe-images', 'middleware' => 'auth'], function () {

    // 1. Static routes first
    Route::post('/store', [ClasseImageController::class, 'store'])->name('classeImages.store');

    // 2. Wildcard routes with static segments
    Route::get('/{classe}/create', [ClasseImageController::class, 'create'])->name('classeImages.create');
    Route::post('/{classe}/setDefault/{image}', [ClasseImageController::class, 'setDefault'])->name('classeImages.setDefault');
    Route::delete('/{image}/destroy', [ClasseImageController::class, 'destroy'])->name('classeImages.destroy');
});

Route::group(['prefix' => 'user-images', 'middleware' => 'auth'], function () {

    // 1. Most specific static routes first
    Route::post('/store', [UserImageController::class, 'store'])->name('userImages.store');

    // 2. Wildcard routes with static segments
    Route::get('/{user}/create', [UserImageController::class, 'create'])->name('userImages.create');
    Route::post('/{user}/setDefault/{image}', [UserImageController::class, 'setDefault'])->name('userImages.setDefault');
    Route::delete('/{image}/destroy', [UserImageController::class, 'destroy'])->name('userImages.destroy');
});

Route::group(['prefix' => 'admin', 'middleware' => 'adminauth'], function () {
    // 1. Static routes with specific names
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name("admins.dashboard");

    // 2. Specific import/export routes
    Route::get('/usersImportForm', [AdminController::class, 'usersImportForm'])->name('users.importForm'); // Open import form
    Route::get('/uesrsExportForm', [AdminController::class, 'usersExportForm'])->name('users.exportForm'); // Open export form

    Route::post('/usersImport', [AdminController::class, 'usersImport'])->name('users.import'); // Handle CSV import
    Route::get('/usersExport', [AdminController::class, 'usersExport'])->name('users.export'); // Export users as CSV

    // Route to display the settings form
    Route::get('/settings/school', [SchoolConfigurationController::class, 'edit'])->name('settings.school.edit');

    // Route to handle the POST/PUT request to save settings
    Route::put('/settings/school', [SchoolConfigurationController::class, 'update'])->name('settings.school.update');
});


Route::group(['prefix' => 'userRoles', 'middleware' => 'auth'], function () {

    Route::get('/{user}/create', [UserRoleController::class, 'create'])->name('userRoles.create');
    Route::put('/{user}/Store', [UserRoleController::class, 'store'])->name("userRoles.store");
    Route::delete('/{user}/destroyAll', [UserRoleController::class, 'destroyAll'])->name("userRoles.destroyAll");
    Route::delete('/{userRole}/destroy', [UserRoleController::class, 'destroy'])->name("userRoles.destroy");
    Route::get('/{user}', [UserRoleController::class, 'index'])->name("userRoles.index");
});

Route::group(['prefix' => 'roles', 'middleware' => 'auth'], function () {

    Route::get('/', [RoleController::class, 'index'])->name("roles.index");

    Route::delete('/destroyAll', [RoleController::class, 'destroyAll'])->name("roles.destroyAll");

    Route::get('/create', [RoleController::class, 'create'])->name('roles.create');

    Route::post('/store', [RoleController::class, 'store'])->name("roles.store");

    Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');

    Route::put('/{role}/update', [RoleController::class, 'update'])->name("roles.update");

    Route::delete('/{role}/destroy', [RoleController::class, 'destroy'])->name("roles.destroy");
});

Route::group(['prefix' => 'subjects', 'middleware' => 'auth'], function () {

    Route::get('/', [SubjectController::class, 'index'])->name("subjects.index");

    Route::delete('/destroyAll', [SubjectController::class, 'destroyAll'])->name("subjects.destroyAll");

    Route::get('/create', [SubjectController::class, 'create'])->name('subjects.create');

    Route::post('/store', [SubjectController::class, 'store'])->name("subjects.store");

    Route::get('/{subject}/edit', [SubjectController::class, 'edit'])->name('subjects.edit');

    Route::put('/{subject}/update', [SubjectController::class, 'update'])->name("subjects.update");

    Route::delete('/{subject}/destroy', [SubjectController::class, 'destroy'])->name("subjects.destroy");
});



Route::group(['prefix' => 'misconduct_acts', 'middleware' => 'adminauth'], function () {

    // R - Index (List all acts)
    Route::get('/', [MisconductActController::class, 'index'])->name("misconduct_acts.index");

    // C - Create Form
    Route::get('/create', [MisconductActController::class, 'create'])->name('misconduct_acts.create');

    // C - Store New Act (Using POST, as is standard for form submissions)
    Route::post('/', [MisconductActController::class, 'store'])->name("misconduct_acts.store");

    // Bulk Delete (Specific to your style)
    Route::delete('/destroyAll', [MisconductActController::class, 'destroyAll'])->name("misconduct_acts.destroyAll");

    // U - Edit Form
    Route::get('/{misconduct_act}/edit', [MisconductActController::class, 'edit'])->name('misconduct_acts.edit');

    // U - Update Existing Act (Using PUT for modification)
    Route::put('/{misconduct_act}', [MisconductActController::class, 'update'])->name("misconduct_acts.update");

    // D - Delete Single Act
    Route::delete('/{misconduct_act}', [MisconductActController::class, 'destroy'])->name("misconduct_acts.destroy");
});

// misconduct reports route
Route::group(['prefix' => 'misconduct_reports', 'middleware' => 'adminauth'], function () {

    // R - Index (List all reports)
    Route::get('/', [MisconductReportController::class, 'index'])->name("misconduct_reports.index");

    // C - Create Form
    Route::get('/create', [MisconductReportController::class, 'create'])->name('misconduct_reports.create');

    // C - Store New Report (Using POST)
    Route::post('/', [MisconductReportController::class, 'store'])->name('misconduct_reports.store');

    // Bulk Delete (Specific to your style)
    Route::delete('/destroyAll', [MisconductReportController::class, 'destroyAll'])->name('misconduct_reports.destroyAll');

    //studentlist
    Route::get('/{seance}/studentList', [MisconductReportController::class, 'studentList'])->name('misconduct_reports.studentList');

    // U - Edit Form
    Route::get('/{misconduct_report}/edit', [MisconductReportController::class, 'edit'])->name('misconduct_reports.edit');
  
    // print report 
    Route::get('/{misconduct_report}/print', [MisconductReportController::class, 'print'])->name('misconduct_reports.print');

    // show preview
    Route::get('/{misconduct_report}/show', [MisconductReportController::class, 'show'])->name('misconduct_reports.show');

      // update status
    Route::post('/{misconduct_report}/updateStatus', [MisconductReportController::class, 'updateStatus'])->name('misconduct_reports.updateStatus');

    // U - Update Existing Report (Using PUT)
    Route::put('/{misconduct_report}', [MisconductReportController::class, 'update'])->name('misconduct_reports.update');

    // D - Delete Single Report     
    Route::delete('/{misconduct_report}', [MisconductReportController::class, 'destroy'])->name('misconduct_reports.destroy');
});

Route::group(['prefix' => 'proposed_actions', 'middleware' => 'adminauth'], function () {

    // R - Index (List all actions)
    Route::get('/', [ProposedActionController::class, 'index'])->name("proposed_actions.index");

    // C - Create Form
    Route::get('/create', [ProposedActionController::class, 'create'])->name('proposed_actions.create');

    // C - Store New Action (Using POST)
    Route::post('/', [ProposedActionController::class, 'store'])->name("proposed_actions.store");

    // Bulk Delete (Specific to your style)
    Route::delete('/destroyAll', [ProposedActionController::class, 'destroyAll'])->name("proposed_actions.destroyAll");

    // U - Edit Form
    Route::get('/{proposed_action}/edit', [ProposedActionController::class, 'edit'])->name('proposed_actions.edit');

    // U - Update Existing Action (Using PUT)
    Route::put('/{proposed_action}', [ProposedActionController::class, 'update'])->name("proposed_actions.update");

    // D - Delete Single Action
    Route::delete('/{proposed_action}', [ProposedActionController::class, 'destroy'])->name("proposed_actions.destroy");
});


// Public routes for fetching data (accessible to all users)
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


Route::group(['prefix' => 'admin'], function () {
    // Show Register/Create Form
    Route::get('/register', [AdminController::class, 'register'])->name('admins.register');

    // store New Admin
    Route::post('/store', [AdminController::class, 'store'])->name("admins.store");
});

Route::get('/get-students/{classe}', [StudentController::class, 'getStudents']);
Route::get('/chart-absence', [AbsenceController::class, 'getChartData']);

// Language Switcher Route
Route::get('/language/{lang}', function ($lang) {
    if (in_array($lang, ['en', 'ar', 'fr', 'z'])) {
        App::setLocale($lang);
        session(['locale' => $lang]);
    }
    return redirect()->back();
})->name('language.switch');
