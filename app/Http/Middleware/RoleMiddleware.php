<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Role;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        // Check if user is logged in
        if (!$user) {
            abort(403, 'Unauthorized'); // cleaner than calling auth() twice
        }

        // Check if user has any of the required roles
        if (!$user->roles()->whereIn('name', $roles)->exists()) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
