<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Check if authenticated user has one of the allowed roles.
     *
     * Usage: middleware('role:super_admin,manager')
     */
    public function handle(Request $request, Closure $next, string...$roles): Response
    {
        if (!auth()->check()) {
            abort(403);
        }

        if (!in_array(auth()->user()->role, $roles)) {
            abort(403, 'Unauthorized — Insufficient permissions.');
        }

        return $next($request);
    }
}
