<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSessionExpired
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
       $lastActivity = session('last_activity_time');
        $maxInactive = config('session.lifetime') * 60;

        if (time() - $lastActivity > $maxInactive) {
            session()->flush(); // Clear the session
            return redirect()->route('login')->withErrors('Your session has expired. Please log in again.');
        }

        return $next($request);
    }
}
