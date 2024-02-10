<?php

namespace App\Http\Controllers\Api\V1;

use Exception;
use App\Models\Role;
use App\Models\User;

use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request, $page_size = 40)
    {
        try {
            if (!Auth::check() || !Auth::user()->hasRole('admin')) {
                return response()->json(['message' => 'غير مسموح. '], 404);
            }
            if ($request->filled("page_size"))
                $page_size = $request->input("page_size");

            $query = User::filter($request)
                ->latest();

           // dd($query->toSql(), $query->getBindings());

            return $query->paginate($page_size);
        } catch (Exception $e) {
            dd($e->getMessage());
            return response()->json(['message' => 'غير متوفر. ' . $e->getMessage()], 404);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request, $role = '')
    {
        try {
            $fields = $request->validate([
                'firstName' => 'required|string',
                'lastName' => 'required|string',
                'email' => 'required|string|unique:_User,email',
                'password' => 'required|string|confirmed'
            ]);

            $role = $role ?? 'student';
            //affect admin role if not already done
            $_role = Role::where('nameEn', $role)->latest()->first();
            $user_role = UserRole::where('role_id', $_role->id)->latest()->first();
            $user = null;
            if (!$user_role) {
                DB::transaction(function () use ($fields, $_role) {
                    //create user account
                    $user = User::create([
                        'firstName' => $fields['firstName'],
                        'lastName' => $fields['lastName'],
                        'email' => $fields['email'],
                        'password' => Hash::make($fields['password'])
                    ]);
                    //and add admin role
                    UserRole::create([
                        'user_id' => $user->id,
                        'role_id' => $_role->id
                    ]);
                });
                $message = 'نجاح التسجيل كمدير للموقع';
            } else {
                $message = 'فشل في التسجيل. المستخدم مدير الموقع موجود بالفعل.';
                return response(['message' => $message], 403);
            }


            $response = [
                'user' => $user,
                'message' => $message,
                'role' => $role
            ];

            return response($response, 201);
        } catch (ValidationException $exception) {
            // Registration failed due to validation error
            $response = [
                'message' => $exception->getMessage()
            ];

            return response($response, 422); // HTTP 422 Unprocessable Entity
        } catch (\Exception $exception) {
            // Registration failed due to a server error
            $response = [
                'message' => 'فشل في التسجيل. الرجاء معاودة المحاولة في وقت لاحق.' . $exception->getMessage()
            ];

            return response($response, 500); // HTTP 500 Internal Server Error
        }
    }

    public function login(Request $request)
    {
        try {
            $fields = $request->validate([
                'email' => 'required|string',
                'password' => 'required|string'
            ]);
            // Check if the user is already authenticated
            if (Auth::check()) {
                // Authentication successful
                $user = Auth::user();
                // Retrieve the user's access token or create a new one if it doesn't exist
                $token = $user->currentAccessToken()->plainTextToken ?? $user->createToken($fields['email'])->plainTextToken;
                $response = [
                    'user' => $user,
                    'token' => $token,
                    'message' => 'انت بالفعل سجلت دخولك.'
                ];
                return response($response, 201);
            } elseif (Auth::attempt($fields)) {
                // Authentication successful
                $user = Auth::user();
                // Retrieve the user's access token or create a new one if it doesn't exist
                $token = $user->currentAccessToken()->plainTextToken  ?? $user->createToken($fields['email'])->plainTextToken;
                $response = [
                    'user' => $user,
                    'token' => $token,
                    'message' => 'تم تسجيل الدخول بنجاح'
                ];
                return response($response, 200);
            } else {
                // Authentication failed (invalid credentials)
                throw ValidationException::withMessages([
                    'email' => 'The provided credentials are incorrect.',
                ]);
            }
        } catch (ValidationException $exception) {
            // Validation exception (invalid credentials)
            return response(['message' => $exception->getMessage()], 401); // HTTP 401 Unauthorized
        } catch (\Exception $exception) {
            // Other exceptions (e.g., server error)
            return response(['message' => 'Login failed. Please try again later.\n'
                . $exception->getMessage()], 500); // HTTP 500 Internal Server Error
        }
    }


    public function logout(Request $request, User $user)
    {
        try {

            if (Auth::user()->id !== $user->id) {
                return response()->json(['message' => 'تم الرفض، لا تمتلك التفويض الكافي للقيام بالعملية'], 403);
            }

            $fields = $request->validate([
                'token' => 'required|string'
            ]);

            if (!Auth::check()) {
                return response()->json(['message' => 'Already logged out'], 201);
            }
            // If the user chooses to logout from all devices
            if ($request->input('logout_all_devices')) {
                $user->tokens()->delete();
                $message = 'Logged out from all your devices';
            }
            // If the user chooses to logout only from the current device
            else {
                $plain_text_token = $fields['token'];
                $personalAccessToken = PersonalAccessToken::findToken($plain_text_token);
                if ($personalAccessToken && $personalAccessToken->tokenable->id == $user->id)
                    $personalAccessToken->delete();
                $message = 'Logged out';
            }

            auth()->logout();

            return response()->json(['message' => $message], 201);
        } catch (\Exception $exception) {
            // Handle any unexpected exceptions here
            // Log the error, send an email, or perform any other actions as needed
            return response()->json(['message' => 'Logout failed. Please try again later.'], 500);
        }
    }




    /**
     * Update the specified user profile in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        // Check if the authenticated user is the same as the user being updated
        if (Auth::user()->id !== $user->id && !Auth::user()->hasRole('admin')) {
            return response()->json(['message' => 'تم الرفض، لا تمتلك التفويض الكافي للقيام بالعملية'], 403);
        }
        try {
            $fields = [];
            if (Auth::user()->hasRole('admin')) {
                // Validate the input data
                $fields = $request->validate([
                    'firstName' => 'required|string',
                    'lastName' => 'required|string',
                ]);
            }

            $response_code = 200;
            // Check if the "change_password" checkbox is checked
            if ($request->has('change_password') && $request->input('change_password') === 'on') {
                if (!$request->filled('old_password')) {
                    throw ValidationException::withMessages([
                        'old_password' => 'Input your current password.',
                    ]);
                }
                if (!Hash::check($request->input('old_password'), $user->password)) {
                    throw ValidationException::withMessages([
                        'old_password' => 'Incorrect password.',
                    ]);
                }
                if (!$request->filled('password')) {
                    throw ValidationException::withMessages([
                        'password' => 'Input your new password.',
                    ]);
                }
                //$fields['password'] = bcrypt($request->input('password'));
                $fields['password'] = Hash::make($request->input('password'));

                $response_code = 201;
            }

            if (count($fields) > 0)
                $user->update($fields);

            return response()->json(['message' => 'تم التعديل بنجاح'], $response_code);
            // HTTP 200 for successful login
        } catch (ValidationException $exception) {
            // Validation exception
            return response(['message' => $exception->getMessage()], 422); // HTTP 401 Unauthorized
        } catch (\Exception $exception) {
            // Other exceptions (e.g., server error)
            return response(['message' => 'لم يتم التعديل بنجاح Please try again later.'], 500); // HTTP 500 Internal Server Error
        }
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        // Check if the authenticated user is the same as the user being deleted
        if (!Auth::user()->hasRole('admin')) {
            return response()->json(['message' => 'تم الرفض، لا تمتلك التفويض الكافي للقيام بالعملية'], 403);
        }

        DB::transaction(function () use ($user) {
            $user->tokens()->delete();
            $user->absences()->delete();
            $user->students()->delete();
            $user->seances()->delete();
            $user->userImages()->delete();
            $user->appels()->delete();
            auth()->logout();
            // Delete the user's account
            $user->delete();
        });


        return response()->json(['message' => 'تم الحدف بنجاح'], 200);
    }
}
