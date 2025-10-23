<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\RoleController as V1RoleController;
use Illuminate\Support\Facades\Auth;
use Exception;


class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, V1RoleController $v1Controller)
    {
        try {
            if (!Auth::check())
                return redirect()->route('users.login');


            $roles = $v1Controller->index($request);

            return view('roles.index', [
                'roles' => $roles,
            ]);
        } catch (Exception $e) {
            return view('error', ["message" => __('vocab.empty')  . $e->getMessage()]);
        }
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            if (!Auth::check() || !Auth::user()->hasRole('admin')) {
                return back()->with('message', __('vocab.not_authorized'));
            }
            return view('roles.create');
        } catch (Exception $e) {
            return view('error', ["message" => __('vocab.action_failure') . $e->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, V1RoleController $controller)
    {
        $response = $controller->store($request);
        // Get the HTTP status code from the response
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array
        if ($status_code === 201) {
            $message = isset($data['message']) ? $data['message'] : __('vocab.action_success');
            return redirect()->route('roles.index')->with('message', $message);
        } else {
            $message = isset($data['message']) ? $data['message'] : __('vocab.action_failure') . ' ' . __('vocab.try_again');
            return back()->withErrors(['message' => $message]);;
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
        try {
            if (!Auth::check() || !Auth::user()->hasRole('admin')) {
                return back()->with('message', __('vocab.not_authorized'));
            }
            return view('roles.edit', ['role' => $role]);
        } catch (Exception $e) {
            return view('error', ["message" => __('vocab.action_failure') . $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role, V1RoleController $controller)
    {
        $response = $controller->update($request, $role);
        // Get the HTTP status code from the response
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array
        if ($status_code === 200) {
            $message = isset($data['message']) ? $data['message'] : __('vocab.action_success');
            return redirect()->route('roles.index')->with('message', $message);
        } else {
            $message = isset($data['message']) ? $data['message'] : __('vocab.action_failure') . ' ' . __('vocab.try_again');
            return back()->withErrors(['message' => $message]);;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role, V1RoleController $controller)
    {
        $response = $controller->destroy($role);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array


        if ($status_code === 200) {
            // update successful
            $message = isset($data['message']) ? $data['message'] : __('vocab.delete_success');
            //get token and save
        } else {
            $message = isset($data['message']) ? $data['message'] : __('vocab.delete_failed');
        }
        return redirect()->route('roles.index')->with('message', $message);
    }

    public function destroyAll(V1RoleController $controller)
    {
        $response = $controller->destroyAll();
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array


        if ($status_code === 200) {
            // update successful
            $message = isset($data['message']) ? $data['message'] : __('vocab.delete_success');
            //get token and save
        } else {
            $message = isset($data['message']) ? $data['message'] : __('vocab.delete_failed');
        }
        return redirect()->route('roles.index')->with('message', $message);
    }
}
