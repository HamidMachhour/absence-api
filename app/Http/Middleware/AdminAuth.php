<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;


class AdminAuth extends Authenticate
{

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            if (Auth::check()) {
                if (Auth::user()->hasRole('admin')) {
                    return route('admins.dashboard');
                } else {
                    return route('users.dashboard');
                }
            } else {
                return route('users.login');
            }
        }
    }
}
