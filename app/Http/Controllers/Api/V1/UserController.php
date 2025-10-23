<?php

namespace App\Http\Controllers\Api\V1;

use Exception;
use App\Models\Role;
use App\Models\User;

use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cookie;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

use League\Csv\Writer;
use SplTempFileObject;
use League\Csv\Reader;

use Illuminate\Support\Facades\Response;

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

            if (!Auth::check()) {
                return response()->json(['message' => __('vocab.not_authorized')], 401);
            }
            if ($request->filled("page_size"))
                $page_size = $request->input("page_size");

            $query = User::query();
            $query->filter($request);

            return $query->paginate($page_size);
        } catch (Exception $e) {
            return response()->json(['message' => __('vocab.empty') . $e->getMessage()], 404);
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
            // 1. Validate the request data.
            $fields = $request->validate([
                'firstName' => 'required|string',
                'lastName' => 'required|string',

                'firstNameFr' => 'nullable|string',
                'lastNameFr' => 'nullable|string',

                'firstNameEn' => 'nullable|string',
                'lastNameEn' => 'nullable|string',

                'firstNameZ' => 'nullable|string',
                'lastNameZ' => 'nullable|string',

                'code' => 'sometimes|string|unique:_User,code',
                'tag_id' => 'sometimes|string|unique:_User,tag_id',
                'email' => 'required|string|unique:_User,email',
                'password' => 'required|string|confirmed'
            ]);

            // 2. Determine the role and check if it exists.
            $roleName = $role ?: 'guest'; // Use the provided role, or 'guest' as default
            $_role = Role::where('nameEn', $roleName)->first();

            // Check if the role actually exists in the database.
            if (!$_role) {
                return response(['message' => "Role '{$roleName}' does not exist."], 404); // HTTP 404 Not Found
            }

            // 3. Set email verification status.
            $emailVerifiedAt = ($roleName === 'admin') ? now() : null;

            $user = null;

            // 4. Use a database transaction to ensure atomicity.
            DB::transaction(function () use ($fields, $_role, $emailVerifiedAt, &$user) {
                // Create the new user account.
                $user = User::create([
                    'firstName' => $fields['firstName'],
                    'lastName' => $fields['lastName'],

                    'firstNameFr' => $fields['firstNameFr'],
                    'lastNameFr' => $fields['lastNameFr'],

                    'firstNameEn' => $fields['firstNameEn'],
                    'lastNameEn' => $fields['lastNameEn'],

                    'firstNameZ' => $fields['firstNameZ'],
                    'lastNameZ' => $fields['lastNameZ'],

                    'email' => $fields['email'],
                    'code' => $fields['code'] ?? null,
                    'tag_id' => $fields['tag_id'] ?? null,
                    'password' => Hash::make($fields['password']),
                    'email_verified_at' => $emailVerifiedAt
                ]);

                // Assign the role to the user.
                UserRole::create([
                    'userId' => $user->id,
                    'roleId' => $_role->id
                ]);
            });

            // 5. Build and return a success response.
            $response = [
                'user' => $user,
                'message' => __('vocab.register_success'),
                'role' => $roleName
            ];

            return response($response, 201); // HTTP 201 Created

        } catch (ValidationException $exception) {
            // Return a 422 for validation errors.
            return response(['message' => $exception->getMessage()], 422);
        } catch (Exception $exception) {
            // Return a 500 for other server errors.
            return response(['message' => __('vocab.register_failure') . ' ' . $exception->getMessage()], 500);
        }
    }


    /**
     * Handle a user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function login(Request $request)
    {
        try {
            // 1. Validate the incoming request data.
            $credentials = $request->validate([
                'email' => ['required', 'string', 'email'],
                'password' => ['required', 'string'],
            ]);

            // 2. Find the user by credentials.
            $user = User::where('email', $credentials['email'])->first();

            // 3. Manually verify password and check user existence.
            if (!$user || !Hash::check($credentials['password'], $user->password)) {
                // Throw a ValidationException for credentials mismatch (Authentication Failure)
                throw ValidationException::withMessages([
                    'email' => __('vocab.credentials_mismatch'),
                ]);
            }

            // 4. Check if the email is verified.
            if (!isset($user->email_verified_at)) {
                // Throw a ValidationException for unverified email
                throw ValidationException::withMessages([
                    'email' => __('vocab.email_not_verified'),
                ]);
            }

            // 5. Delete all previous tokens (Sanctum/API standard).
            $user->tokens()->delete();

            // 6. Create a new token for the authenticated user.
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'user' => $user,
                'token' => $token,
                'message' => __('vocab.login_success'),
            ], 200);
        } catch (ValidationException $e) {
            // Return 401 for authentication failure (which includes validation errors in this context)
            return response()->json([
                'message' => __('vocab.login_failure'),
                // Optionally include specific validation errors for the frontend
                'errors' => $e->errors()
            ], 401);
        } catch (Exception $e) {
            // Catch all other exceptions (e.g., database error)
            return response()->json([
                'message' => __('vocab.login_failure'),
                'error' => __('vocab.try_again')
            ], 500);
        }
    }

    /**
     * Handle a user logout request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    // In your V1UserController@logout function

    public function logout(Request $request)
    {
        try {
            $user = Auth::user();

            // 1. Check if the user is actually authenticated
            if (!$user) {
                return response()->json(['message' => __('vocab.already_logged_out')], 201);
            }

            // 2. Revoke the token(s) based on request
            if ($request->input('logout_all_devices')) {
                // Revoke all tokens for the user (This is safe and won't cause the error)
                $user->tokens()->delete();
                $message = __('vocab.logout_success') . ' ' . __('vocab.all_devices');
            } else {
                // Revoke only the current token used for this request

                // --- CRITICAL FIX: Add Null Check ---
                $currentAccessToken = $user->currentAccessToken();

                if ($currentAccessToken) {
                    // If the token is found, delete it.
                    $currentAccessToken->delete();
                    $message = __('vocab.logout_success');
                } else {
                    // If currentAccessToken is null, the user is authenticated via some other means (like a web session)
                    // or the token has already been revoked. Treat as already logged out.
                    $message = __('vocab.already_logged_out');
                }
                // ------------------------------------
            }

            return response()->json(['message' => $message], 201);
        } catch (\Exception $exception) {
            // You might want to log the specific error here for debugging
            return response()->json(['message' => __('vocab.logout_failure') . ' ' . __('vocab.try_again')], 500);
        }
    }



    /**
     * Update the specified user profile in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */

    // ... other necessary imports for your User model, etc.

    public function update(Request $request, User $user)
    {
        // Check if the authenticated user is the same as the user being updated
        // or if the authenticated user is an admin
        $isAdmin = Auth::user()->hasRole('admin');
        $isUpdatingSelf = Auth::user()->id === $user->id;

        if (!$isUpdatingSelf && !$isAdmin) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }

        try {
            $fields = [];

            if ($isAdmin) {
                // Admin-specific validation
                $fields = $request->validate([
                    'firstName' => 'required|string',
                    'lastName' => 'required|string',

                    'firstNameFr' => 'nullable|string',
                    'lastNameFr' => 'nullable|string',

                    'firstNameEn' => 'nullable|string',
                    'lastNameEn' => 'nullable|string',

                    'firstNameZ' => 'nullable|string',
                    'lastNameZ' => 'nullable|string',

                    // The following assume you have these fields on your User model
                    'code' => ['required', 'string',  'unique:_User,code,' . $user->id],
                    'tag_id' => ['required', 'string', 'unique:_User,tag_id,' . $user->id],
                ]);
            } else {
                // User-specific validation (for updating their own profile)
                $fields = $request->validate([
                    'firstName' => 'required|string',
                    'lastName' => 'required|string',

                    'firstNameFr' => 'nullable|string',
                    'lastNameFr' => 'nullable|string',

                    'firstNameEn' => 'nullable|string',
                    'lastNameEn' => 'nullable|string',

                    'firstNameZ' => 'nullable|string',
                    'lastNameZ' => 'nullable|string',
                    // Add your own user-specific validation here (e.g., 'email' unique rule)
                ]);
            }

            $response_code = 200;

            // --- Password Change Logic ---
            if ($request->has('change_password') && $request->input('change_password') === 'on') {

                // 1. Determine if we need to check the old password
                $oldPasswordNeeded = !$isAdmin || $isUpdatingSelf;

                if ($oldPasswordNeeded) {
                    // For a non-admin user updating self OR an admin updating self
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
                }

                // 2. Validate the new password (required and confirmation match)
                $passwordFields = $request->validate([
                    'password' => 'required|string|min:8|confirmed', // 'confirmed' checks for 'password_confirmation' field
                ]);

                // 3. Hash and store the new password
                $fields['password'] = Hash::make($passwordFields['password']);
                $response_code = 201;
            }

            // Only update if there are fields to update
            if (count($fields) > 0) {
                $user->update($fields);
            }

            return response()->json(['message' => __('vocab.update_success')], $response_code);
        } catch (ValidationException $exception) {
            // Validation exception
            return response()->json(['message' => $exception->getMessage(), 'errors' => $exception->errors()], 422);
        } catch (\Exception $exception) {
            // Other exceptions (e.g., server error)
            // Log the exception for debugging
            // \Log::error('User update error: ' . $exception->getMessage(), ['userId' => Auth::id(), 'target_user_id' => $user->id]);
            return response()->json(['message' => __('vocab.update_failure') . ' ' . __('vocab.try_again')], 500);
        }
    }
    // function to activate user account
    public function activate(User $user)
    {
        // Check if the authenticated user is the same as the user being updated
        if (!Auth::user()->hasRole('admin')) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }
        if ($user->email_verified_at) {
            return response()->json(['message' => __('vocab.activation_success')], 200);
        }
        $user->email_verified_at = now();
        $user->save();
        return response()->json(['message' => __('vocab.activation_success')], 200);
    }
    // function to refute user account
    public function refute(User $user)
    {
        // Check if the authenticated user is the same as the user being updated
        if (!Auth::user()->hasRole('admin')) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }
        if (!$user->email_verified_at) {
            return response()->json(['message' => __('vocab.deactivation_success')], 200);
        }
        $user->email_verified_at = null;
        $user->save();
        return response()->json(['message' =>  __('vocab.deactivation_success')], 200);
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
            return response()->json(['message' => __('vocab.delete_failure') . ' ' . __('vocab.not_authorized')], 403);
        }

        DB::transaction(function () use ($user) {
            $user->tokens()->delete();
            $user->absenceStudents()->delete();
            $user->students()->delete();
            $user->seances()->delete();
            $user->userImages()->delete();
            $user->attendances()->delete();
            // Delete the user's account
            $user->delete();
        });


        return response()->json(['message' => __('vocab.delete_success')], 200);
    }

    //function to export users to excel or csv file
    public function export(Request $request)
    {
        // ckeck if the user is admin
        if (!Auth::check() || !Auth::user()->hasRole('admin')) {
            return response()->json(['message' => __('vocab.not_authorized')], 404);
        }
        try {

            $fileName = 'users_export_' . date('Y_m_d_H_i_s') . '.csv';
            // get users according to filters
            $users = User::filter($request)
                ->latest()
                ->get();
            // create csv file
            $csv = Writer::createFromFileObject(new SplTempFileObject());
            // add header to csv file
            $headers = ['id', 'firstName', 'lastName', 'firstNameFr', 'lastNameFr', 'firstNameEn', 'lastNameEn', 'firstNameZ', 'lastNameZ', 'email', 'code', 'tag_id', 'email_verified_at', 'role'];
            $csv->insertOne($headers);
            // add users to csv file
            foreach ($users as $user) {
                $itemData = [
                    $user->id,
                    $user->firstName,
                    $user->lastName,
                    $user->firstNameFr,
                    $user->lastNameFr,
                    $user->firstNameEn,
                    $user->lastNameEn,
                    $user->firstNameZ,
                    $user->lastNameZ,
                    $user->email,
                    $user->code,
                    $user->tag_id,
                    $user->email_verified_at,
                    $user->role()
                ];
                $csv->insertOne($itemData);
            }
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename=' . $fileName,
            ];

            return Response::make($csv->toString(), 200, $headers);
        } catch (Exception $e) {
            return response()->json(['message' => __('vocab.empty') . $e->getMessage()], 404);
        }
    }
    //function to import users from excel or csv file
    public function import(Request $request)
    {
        // ckeck if the user is admin
        if (!Auth::check() || !Auth::user()->hasRole('admin')) {
            return response()->json(['message' => __('vocab.not_authorized')], 404);
        }
        try {
            $fields = $request->validate([
                'file' => 'required|mimes:csv,txt|max:10240',
            ]);

            $fileName_summary = 'import_users_summary_' . time() . '.csv';
            $csv_summary = Writer::createFromFileObject(new SplTempFileObject());
            $headers = ['info', 'details', 'status'];
            $csv_summary->insertOne($headers);

            $file = $request->file('file');
            $fileName = 'users-' . Str::uuid() . '.csv';
            $filePath = $file->storeAs('imports', $fileName, 'public');

            $csv = Reader::createFromPath(storage_path('app/public/' . $filePath), 'r');
            $csv->setHeaderOffset(0);

            $records = $csv->getRecords(); // Get all records as an iterator object

            $importedCount = 0;
            $skippedCount = 0;

            foreach ($records as $record) {
                // Validate each record
                if (isset($record['firstName'], $record['lastName'], $record['email'])) {
                    // Check if the user already exists
                    $existingUser = User::where('email', $record['email'])->first();
                    if (!$existingUser) {
                        try {
                            // create transaction to ensure data integrity
                            DB::transaction(function () use ($record, $csv_summary, $importedCount) {
                                // Assign role or default to 'guest'
                                $roleName = ($record['role'] || $record['role'] === 'admin') ?: 'guest';

                                $_role = Role::where('nameEn', $roleName)->latest()->first();
                                // Create user account
                                $record['tag_id'] = $record['tag_id'] ?: rand(10000, 100000);
                                $record['code'] = $record['code'] ?: rand(10000, 100000);
                                $record['password'] = Hash::make('password');
                                unset($record['role']);

                                $user = User::create($record);
                                //and add role
                                UserRole::create([
                                    'userId' => $user->id,
                                    'roleId' => $_role->id
                                ]);

                                $csv_summary->insertOne(['new', $user->id, 'imported']);
                                $importedCount++;
                            });
                        } catch (\Exception $exception) {
                            $csv_summary->insertOne(['error', $exception->getMessage(), 'skipped']);
                            $skippedCount++;
                        }
                    } else {
                        $csv_summary->insertOne(['exists', implode(', ', $record), 'skipped']);
                        $skippedCount++;
                    }
                }
            }

            $message = __('vocab.import_success') . ". Imported: $importedCount, Skipped (already exist): $skippedCount";

            File::delete(storage_path('app/public/' . $filePath));

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename=' . $fileName_summary,
                'imported' => $importedCount,
                'skipped' => $skippedCount,
                'message' => $message
            ];

            return Response::make($csv_summary->toString(), 200, $headers);
        } catch (ValidationException $exception) {
            // Validation exception
            return response(['message' => $exception->getMessage()], 422); // HTTP 401 Unauthorized
        }
    }
}
