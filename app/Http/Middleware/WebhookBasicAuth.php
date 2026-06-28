<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WebhookBasicAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $username = env('WEBHOOK_USERNAME', 'Ramin');
        $password = env('WEBHOOK_PASSWORD', 'Ramin');

        if (
            $request->getUser() !== $username ||
            $request->getPassword() !== $password
        ) {
            return response('Unauthorized.', 401);
        }

        return $next($request);
    }
}
