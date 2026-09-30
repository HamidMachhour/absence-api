<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Api\V1\UserRoleController as V1UserRoleController;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;
use App\Models\User;

class UserRoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, User $user, V1UserRoleController $v1Controller)
    {
        try {
            if (!Auth::check())
                return redirect()->route('users.login');


            $userRoles = $v1Controller->index($request,$user);

        

            return view('userRoles.index', [
                'userRoles' => $userRoles,
                'user' => $user
            ]);
        } catch (Exception $e) {
            return view('error', ["message" => __('vocab.no_data')  . $e->getMessage()]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(User $user)
    {
        try {
            if (!Auth::check() || !Auth::user()->hasRole('admin')) {
                return back()->with('message', __('vocab.not_authorized'));
            }

            $roles=Role::get();
            return view('userRoles.create',['user'=>$user,'roles'=>$roles]);
        } catch (Exception $e) {
            return view('error', ["message" =>__('vocab.action_failure') . $e->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, User $user, V1UserRoleController $controller)
    {
        $response = $controller->store($request,$user);
        // Get the HTTP status code from the response
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array
        if ($status_code === 201) {
            $message = isset($data['message']) ? $data['message'] : __('vocab.action_success');
            return redirect()->route('userRoles.index',['user'=>$user])->with('message', $message);
        } else {
            $message = isset($data['message']) ? $data['message'] : __('vocab.action_failure') . ' ' . __('vocab.try_again');
            return back()->withErrors(['message' => $message]);;
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
    public function destroy(UserRole $userRole, V1UserRoleController $controller)
    {
        $response = $controller->destroy($userRole);
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
        return redirect()->route('userRoles.index',['user'=>$userRole->userId])->with('message', $message);

    }

    public function destroyAll(User $user, V1UserRoleController $controller)
    {
        $response = $controller->destroyAll($user);
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
        return redirect()->route('userRoles.index',['user'=>$user->id])->with('message', $message);

    }
}
