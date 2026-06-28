<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckSuperUser
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('employee.dashboard')
                ->with('delete_msg', 'Unauthorized – bitte einloggen.');
        }

        // Prüfen in der user_rolls Tabelle
        $hasSuper = DB::table('user_rolls')
            ->where('user_id', $user->name)
            ->where('item_id', 'Super')
            ->exists();

        if (!$hasSuper) {
            // 👇 Redirect zurück + Session-Msg für Toastr
            return redirect()->route('employee.dashboard')
                ->with('delete_msg', 'Unauthorized – nur Super User dürfen hier rein!');
        }

        return $next($request);
    }
}
