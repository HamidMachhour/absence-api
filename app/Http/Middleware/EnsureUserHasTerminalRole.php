<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserHasTerminalRole
{
    /**
     * Handle an incoming request.
     *
     * If the user is not authenticated or does not have the `terminal` role,
     * return a JSON 403 when the request expects JSON, otherwise abort with 403.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // If user is not authenticated -> redirect to login (or JSON 401)
        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json(['message' => __('vocab.not_authorized') ?: 'Unauthorized.'], 401);
            }
            return redirect()->route('users.login')->with('message', __('vocab.not_authorized') ?: 'Unauthorized.');
        }

        // Ensure the User model exposes hasRole and allow when user has either 'admin' OR 'terminal'
        $hasHasRole = method_exists($user, 'hasRole');
        $isAllowed = $hasHasRole && ($user->hasRole('admin') || $user->hasRole('terminal'));

        if (!$isAllowed) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => __('vocab.not_authorized') ?: 'Unauthorized.'
                ], 403);
            }

            return redirect()->route('home')->with('message', __('vocab.not_authorized') ?: 'Unauthorized.');
        }

        return $next($request);
    }
}
