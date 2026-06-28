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
    
        $user = DB::table('user_rolls')
            ->join('users', 'users.name', '=', 'user_rolls.user_id')
            ->where('user_rolls.user_id', '=', auth()->user()->name)
            ->where('user_rolls.is_read', '=', 'on')
            ->where('user_rolls.item_id', '=', 'Invoice')
            ->select('user_rolls.user_id')
            ->value('user_rolls.user_id');
    
        // Check if the user is authorized (user exists in the user_rolls table with correct permissions)
        if (auth()->user()->name == $user) {
            return $next($request);
        }
    
        return redirect('/notAdmin');
    }
}
