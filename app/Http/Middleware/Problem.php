<?php

namespace App\Http\Middleware;

use Auth;
use DB;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Problem
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

        $user = DB::table('user_rolls')
        ->join('users', 'users.name', '=', 'user_rolls.user_id')
        ->where('user_rolls.user_id', '=', auth()->user()->id)
        ->where('user_rolls.is_read', '=', 1)
        ->orWhere('user_rolls.is_read', '=', 'on')
        ->where('user_rolls.item_id', '=', 'Problem' )
        ->select('user_rolls.user_id')
        ->value('user_rolls.user_id');
        
        if (auth()->user()->name == $user) {
         return $next($request);
        }
        return redirect('/notAdmin');
       
    }
}
