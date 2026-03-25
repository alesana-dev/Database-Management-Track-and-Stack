<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (! in_array($user->role, [
            User::ROLE_SUPER_ADMIN,
            User::ROLE_CHAPTER_ADMIN,
        ], true)) {
            Auth::logout();
            abort(403, 'Admins only.');
        }

        return $next($request);
    }
}
