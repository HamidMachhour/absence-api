<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\UserRole;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Illuminate\Validation\Rule;

use Exception;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            if (!Auth::check() && !Auth::user()->hasRole('admin')) {
                return response()->json(['message' => __('vocab.not_authorized')], 404);
            }

            $roles = Role::get();

            return $roles;
        } catch (Exception $e) {
            return response()->json(['message' => __('vocab.no_data') . $e->getMessage()], 404);
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
    public function store(Request $request)
    {
        if (!Auth::check() || !Auth::user()->hasRole('admin')) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }

        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'required|string',
            'nameFr' => 'nullable|string',
            'nameEn' => 'required|string',
            'nameZ' => 'nullable|string',
        ]);

        try {
            // 💡 Check if we already have this role
            $existingRole = Role::where('nameEn', $validatedData['nameEn'])
                ->where('name', $validatedData['name'])
                ->first();

            if ($existingRole) {
                return response()->json([
                    'message' => __('vocab.role_exist')
                ], 409); // Use 409 Conflict for duplicate resource
            }

            // Create a new Role object and assign the validated data
            $role = Role::create($validatedData);

            // Return a response
            return response()->json([
                'message' => __('vocab.action_success'),
                'role' => $role
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
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */


    public function update(Request $request, Role $role)
    {
        // Check for authorization first. Use a 403 status code for permission errors.
        if (!Auth::check() || !Auth::user()->hasRole('admin')) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }

        // Validate the request data. Use the 'Rule::unique' to check for uniqueness
        // while ignoring the current role's ID.
        $validatedData = $request->validate([
            'name' => ['required', 'string', Rule::unique('_Role', 'name')->ignore($role->id)],
            'nameFr' => ['nullable', 'string', Rule::unique('_Role', 'nameFr')->ignore($role->id)],
            'nameEn' => ['required', 'string', Rule::unique('_Role', 'nameEn')->ignore($role->id)],
            'nameZ' => ['nullable', 'string', Rule::unique('_Role', 'nameZ')->ignore($role->id)],

        ]);

        // Update the role with the validated data
        $role->update($validatedData);

        // Return a success response. Use a 200 status code for a successful update
        // and a relevant success message.
        return response()->json(['message' => __('vocab.update_success')], 200);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        // Check for authorization first
        if (!Auth::check() || !Auth::user()->hasRole('admin')) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }

        // The 'deleting' event on the Role model will handle cascading soft deletes for UserRole pivot entries.
        $role->delete();

        // Return a successful response. 200 OK or 204 No Content are good choices.
        return response()->json(['message' => __('vocab.delete_success')], 200);
    }



    public function destroyAll()
    {
        // Check for authorization first
        if (!Auth::check() || !Auth::user()->hasRole('admin')) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }

        try {
            // Find all roles that are currently in use
            $usedRoleIds = DB::table('UserRole')->whereNull('deleted_at')->pluck('roleId')->unique();

            // Fetch the roles to be deleted
            $rolesToDelete = Role::whereNotIn('id', $usedRoleIds)->get();

            // Call delete on each model instance to ensure model events are fired
            $rolesToDelete->each->delete();

            return response()->json(['message' => __('vocab.delete_success')], 200);
        } catch (\Exception $e) {
            // Return a generic error message for any exceptions
            return response()->json(['message' => __('vocab.delete_failure') . $e->getMessage()], 500);
        }
    }
}
