<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('auth.show');
        }

        if (!$user->roles()->whereIn('name', $roles)->exists()) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
