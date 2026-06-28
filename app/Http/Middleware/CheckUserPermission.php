<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUserPermission
{
    public function handle(Request $request, Closure $next, string $item, string $action = 'read')
    {
        $user = $request->user();

        if (!$user) {
            abort(401);
        }

        if (!$user->hasPermission($item, $action)) {
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}