<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\ValidationException;

use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        try {
            $fields = $request->validate([
                'name' => 'string',
                'email' => 'required|string|unique:users,email',
                'password' => 'required|string|confirmed'
            ]);

            $user = User::create([
                'name' => isset($fields['name']) ? $fields['name'] : $fields['email'],
                'email' => $fields['email'],
                'password' => bcrypt($fields['password'])
            ]);

            $response = [
                'user' => $user,
                'message' => 'Registration success'
            ];

            return response($response, 201);
        } catch (ValidationException $exception) {
            // Registration failed due to validation error
            $response = [
                'message' => 'Registration failed. The email has already been taken.'
            ];

            return response($response, 422); // HTTP 422 Unprocessable Entity
        } catch (\Exception $exception) {
            // Registration failed due to a server error
            $response = [
                'message' => 'Registration failed. Please try again later.'
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
                // Retrieve the user from the database
                $token = $user->currentAccessToken()->plainTextToken;
                $response = [
                    'user' => $user,
                    'token' => $token,
                    'message' => 'You are already logged in.'
                ];
                return response($response, 201);
            } elseif (Auth::attempt($fields)) {
                // Authentication successful
                $user = Auth::user();
                // Retrieve the user from the database
                $token = $user->currentAccessToken()->plainTextToken;
                $response = [
                    'user' => $user,
                    'token' => $token,
                    'message' => 'Connection success'
                ];
                return response($response, 200);
            } else {
                // Authentication failed (invalid credentials)
                throw ValidationException::withMessages([
                    'email' => 'The provided credentials are incorrect.',
                ]);
            }



            // HTTP 200 for successful login
        } catch (ValidationException $exception) {
            // Validation exception (invalid credentials)
            return response(['message' => $exception->getMessage()], 401); // HTTP 401 Unauthorized
        } catch (\Exception $exception) {
            // Other exceptions (e.g., server error)
            return response(['message' => 'Login failed. Please try again later.'], 500); // HTTP 500 Internal Server Error
        }
    }


    public function logout(Request $request)
    {
        try {
            $user = Auth::user();
            // If the user chooses to logout from all devices
            if ($request->input('logout_all_devices')) {
                $user->tokens()->delete();
                auth()->logout();
                $message = 'Logged out from all your devices';
            }
            // If the user chooses to logout only from the current device
            else {
                auth()->logout();
                $message = 'Logged out';
            }

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
        if (Auth::user()->id !== $user->id) {
            return response()->json(['message' => 'تم الرفض، لا تمتلك التفويض الكافي للقيام بالعملية'], 403);
        }

        // Validate the input data
        $request->validate([
            'name' => 'string',
          //  'email' => 'email|unique:users,email,' . $user->id, // Ignore the current user's email during validation
          //  'password' => 'confirmed',
        ]);

        // Update the user's data
        $user->update($request->only(['name']));

        return response()->json(['message' => 'تم التعديل بنجاح'], 200);
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
        if (Auth::user()->id !== $user->id) {
            return response()->json(['message' => 'تم الرفض، لا تمتلك التفويض الكافي للقيام بالعملية'], 403);
        }

        $user->tokens()->delete();
        auth()->logout();
        // Delete the user's account
        $user->delete();

        return response()->json(['message' => 'تم الحدف بنجاح'], 200);
    }
}
