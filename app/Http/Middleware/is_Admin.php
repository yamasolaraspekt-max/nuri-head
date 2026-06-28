<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class is_Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // FIX P0-08: gefixtes Muster - generischer Admin-Gate = Super-Admin (is_admin).
        // Vorher: Vergleich auf die nicht-existente Spalte users.roll -> immer false -> sperrte ALLE aus.
        if (auth()->user()->is_admin) {
            return $next($request);
        }

        abort(403, 'Nur fuer Administratoren.');
    }
}
