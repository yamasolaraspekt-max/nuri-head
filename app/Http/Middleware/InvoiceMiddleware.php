<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use DB;

class InvoiceMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // FIX P0-07: gefixtes Muster - user_rolls.user_id = users.id (FK), Flag tinyint(1)=1 (NICHT 'on'),
        // is_admin-Bypass. Der vorherige Join auf users.name + Flag 'on' ohne Bypass haette ALLE ausgesperrt.
        $allowed = (bool) auth()->user()->is_admin || DB::table('user_rolls')
            ->where('user_rolls.user_id', '=', auth()->id())
            ->where('user_rolls.is_read', '=', 1)
            ->where('user_rolls.item_id', '=', 'Invoice')
            ->exists();

        if ($allowed) {
            return $next($request);
        }

        abort(403, 'Keine Berechtigung fuer Rechnungen.');
    }
}
