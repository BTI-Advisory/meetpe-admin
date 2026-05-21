<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ExtendAdminTimeout
{
    public function handle(Request $request, Closure $next)
    {
        set_time_limit(120);
        return $next($request);
    }
}
