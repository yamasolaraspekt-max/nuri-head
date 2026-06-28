<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyFusionToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('X-Fusion-Token');
        $expected = config('services.fusion_forms.token');

        if (!$token || $token !== $expected) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
