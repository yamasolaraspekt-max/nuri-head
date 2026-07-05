<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Feature-Gate für das Heizkörper-Modul (M4). Läuft VOR 'auth': bei OFF sofort 404,
 * damit garantiert kein Controller/keine Query auf die (bis M5) nur in ticket_testing
 * vorhandenen HK-Tabellen erreicht wird. Freischaltung über config('features.heizkoerper')
 * bzw. env HEIZKOERPER_MODULE_ENABLED (Default false).
 */
class EnsureHeizkoerperEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(config('features.heizkoerper'), 404);

        return $next($request);
    }
}
