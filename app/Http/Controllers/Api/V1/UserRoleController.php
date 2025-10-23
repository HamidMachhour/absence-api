<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\UserRole;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Exception;

class UserRoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, User $user)
    {
        try {
            if (!Auth::check() && !Auth::user()->hasRole('admin')) {
                return response()->json(['message' => __('vocab.not_authorized')], 404);
            }

            $userRoles = UserRole::where('userId', $user->id)
                ->get();

            return $userRoles;
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, User $user)
    {
        if (!Auth::check() || !Auth::user()->hasRole('admin')) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }

        // Validate the request data
        $validatedData = $request->validate([
            'roleId' => 'required|string',
        ]);

        try {
            // 💡 Check if the user already has this role
            $existingRole = UserRole::where('userId', $user->id)
                ->where('roleId', $validatedData['roleId'])
                ->first();

            if ($existingRole) {
                return response()->json([
                    'message' => __('vocab.role_exist')
                ], 409); // Use 409 Conflict for duplicate resource
            }

            // Create a new UserRole object and assign the validated data
            $validatedData['userId'] = $user->id;
            $userRole = UserRole::create($validatedData);

            // Return a response
            return response()->json([
                'message' => __('vocab.action_success'),
                'userRole' => $userRole
            ], 201);
        } catch (Exception $exception) {
            return response()->json([
                'message' => __('vocab.task_failed') . ' ' . __('vocab.try_again') . $exception->getMessage()
            ], 404);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\UserRole  $userRole
     * @return \Illuminate\Http\Response
     */
    public function show(UserRole $userRole)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\UserRole  $userRole
     * @return \Illuminate\Http\Response
     */
    public function edit(UserRole $userRole)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\UserRole  $userRole
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UserRole $userRole)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UserRole  $userRole
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserRole $userRole)
    {
        try {
            if (!Auth::check() || !Auth::user()->hasRole('admin')) {
                return response()->json(['message' => __('vocab.not_authorized')], 404);
            }
            // Soft delete the userRole
            $userRole->delete();
            // Return a response
            return response()->json(['message' => __('vocab.delete_success')], 200);
        } catch (Exception $e) {
            return response()->json(['message' => __('vocab.delete_failure') . $e->getMessage()], 404);
        }
    }

    public function destroyAll(User $user)
    {
        // Check for authorization first
        if (!Auth::check() || !Auth::user()->hasRole('admin')) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }

        // Delete all roles for the specified user
        // The `delete()` method will perform a permanent deletion
        UserRole::where('userId', $user->id)->delete();

        // Return a success response
        return response()->json(['message' => __('vocab.delete_success')], 200);
    }
}
