<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Route;
use SebastianBergmann\Environment\Console;
use Symfony\Component\Console\Event\ConsoleCommandEvent;

class isAdmin
{
    public function handle($request, Closure $next)
    {
       
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = DB::table('user_rolls')
        ->join('users', 'users.name', '=', 'user_rolls.user_id')
        ->where('user_rolls.user_id', '=', auth()->user()->name)
        ->where('user_rolls.is_read', '=', 'on')
        ->where('user_rolls.item_id', '=', 'Invoice' )
        ->select('user_rolls.user_id')
        ->value('user_rolls.user_id');
        
        if (auth()->user()->name == $user) {
         return $next($request);
        }
        return redirect('/notAdmin');
       
    }
}
