<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Usage: ->middleware('role:admin,manager')  OR ->middleware('role:admin')
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$roles
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        // Redirect guests to login
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        // If no roles were provided, deny access
        if (empty($roles)) {
            abort(403, 'Unauthorized');
        }

        $userRole = strtolower((string) (Auth::user()->role ?? ''));

        // Normalize allowed roles and check membership
        $allowed = array_map(function ($r) {
            return strtolower(trim($r));
        }, $roles);

        if (! in_array($userRole, $allowed, true)) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}