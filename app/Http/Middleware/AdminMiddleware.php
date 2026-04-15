<?php

namespace App\Http\Middleware;

use App\Models\UserRoles;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            // Query the user_roles table to check if the user has the ADMIN role
            $userHasAdminRole = UserRoles::where('user_id', Auth::user()->id)
                ->where('role_id', 3) // Assuming role_id 3 corresponds to the ADMIN role
                ->exists();

            if ($userHasAdminRole) {
                return $next($request);
            }
        }

        // Redirect or abort here if the user is not authorized
        // For example, redirect to login or show a 403 Forbidden page
        return redirect()->route('login')->with("acces", "ce compte n'a pas accès"); // Replace with appropriate redirect or response
    }
}

