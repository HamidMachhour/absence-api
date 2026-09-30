<?php

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\SeanceController;
use App\Http\Controllers\Api\V1\AttendanceController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\StudentController;
use App\Http\Controllers\Api\V1\ClasseImageController;
use App\Http\Controllers\Api\V1\ClasseController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


/*
| Public routes
*/

//Authentication

Route::group(['prefix' => 'auth', 'namespace' => 'App\Http\Controllers\Api\V1'], function () {
    Route::post('/register', [UserController::class, 'register']);
    Route::post('/login', [UserController::class, 'login']);
});
// Stores
Route::group([
    'prefix' => 'v1/classes',
    'namespace' => 'App\Http\Controllers\Api\V1'
], function () {
    Route::get('/{page_size?}', [ClasseController::class, 'index'])
        ->where('page_size', '[0-9]+');
});

// students
Route::group([
    'prefix' => 'v1/students',
    'namespace' => 'App\Http\Controllers\Api\V1'
], function () {
    Route::get('/', [StudentController::class, 'index']);
});


/*
| Protected routes
*/

//Authentication
Route::group([
    'prefix' => 'auth', 'namespace' => 'App\Http\Controllers\Api\V1',
    'middleware' => ['auth:sanctum']
], function () {

    Route::post('/logout', [UserController::class, 'logout']);
});

//classes

Route::prefix('v1/my-classes')
    ->namespace('App\Http\Controllers\Api\V1')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::post('/', [ClasseController::class, 'classe']);
        Route::put('/', [ClasseController::class, 'update']);
        Route::delete('/', [ClasseController::class, 'destroy']);

        Route::get('/{page_size?}', [ClasseController::class, 'show'])
        ->where('page_size', '[0-9]+');

    });

//students
Route::prefix('v1/my-students')
    ->namespace('App\Http\Controllers\Api\V1')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::post('/', [StudentController::class, 'classe']);
        Route::put('/', [StudentController::class, 'update']);
        Route::delete('/', [StudentController::class, 'destroy']);

        Route::get('/{page_size?}', [StudentController::class, 'show'])
        ->where('page_size', '[0-9]+');
        Route::post('/upload', [StudentController::class, 'upload']);
        Route::get('/download', [StudentController::class, 'download']);
    });

//store images
Route::prefix('v1/store-images')
->namespace('App\Http\Controllers\Api\V1')
->middleware('auth:sanctum')
->group(function () {
    Route::post('/', [ClasseImageController::class, 'classe']);
    Route::put('/', [ClasseImageController::class, 'update']);
    Route::delete('/', [ClasseImageController::class, 'destroy']);

    Route::get('/{page_size?}', [ClasseImageController::class, 'show'])
    ->where('page_size', '[0-9]+');
    Route::post('/upload', [ClasseImageController::class, 'upload']);
    Route::get('/download', [ClasseImageController::class, 'download']);
});

Route::prefix('v1/seances')
    ->namespace('App\Http\Controllers\Api\V1')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('/{seanceId}/roster', [SeanceController::class, 'roster']);
        Route::post('/{seance}/attendance', [AttendanceController::class, 'submitForSeance']);
    });

Route::prefix('v1/teachers/me')
    ->namespace('App\Http\Controllers\Api\V1')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('/seances', [SeanceController::class, 'mySeances']);
    });



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Fallback route for handling 404 responses
Route::fallback(function () {
    return response()->json([
        'message' => 'Endpoint not found.'
    ], 404);
});
