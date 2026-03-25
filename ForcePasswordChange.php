<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    public function handle(Request $request, Closure $next): Response
    {
        // Not logged in → ignore
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        // Allow auth + password routes to avoid redirect loops
        if (
            $request->routeIs('password.*') ||
            $request->routeIs('logout') ||
            $request->routeIs('login') ||
            $request->routeIs('register')
        ) {
            return $next($request);
        }

        // Super Admins are not forced here
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Force ONLY if first_login = true
        if ((bool) $user->first_login === true) {
            return redirect()->route('password.change');
        }

        return $next($request);
    }
}
