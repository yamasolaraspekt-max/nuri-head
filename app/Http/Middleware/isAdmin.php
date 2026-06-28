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

        // user_rolls.user_id = users.id (FK); Flags sind tinyint(1) (1/0, nicht 'on').
        // Super-Admins (is_admin) dürfen generell durch.
        $allowed = (bool) auth()->user()->is_admin || DB::table('user_rolls')
            ->where('user_rolls.user_id', '=', auth()->id())
            ->where('user_rolls.is_read', '=', 1)
            ->where('user_rolls.item_id', '=', 'Invoice')
            ->exists();

        if ($allowed) {
            return $next($request);
        }
        return redirect('/notAdmin');
       
    }
}
