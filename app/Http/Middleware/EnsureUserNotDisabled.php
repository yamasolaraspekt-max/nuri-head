<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

/**
 * Z2-W0-9 · Eine laufende Sitzung endet, sobald das Konto deaktiviert wird.
 *
 * **Warum eine Middleware und nicht das Löschen der Session-Zeile:** `logOffUser` versuchte genau
 * das und war wirkungslos — es griff auf einen falschen Session-Schlüssel zu, und eine
 * `sessions`-Tabelle gibt es in dieser Installation gar nicht. Eine Sperre, die vom Session-Treiber
 * abhängt, ist keine. Diese Middleware prüft bei JEDEM Request; damit endet die Sitzung spätestens
 * beim nächsten Klick, unabhängig davon, wo die Session liegt.
 *
 * **Kein Abschalter:** eine Auth-Sperre, die man per Konfiguration abschalten kann, ist keine
 * Sperre. Der Rückweg ist der Commit (so steht es im Auftrag).
 */
class EnsureUserNotDisabled
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && Schema::hasColumn('users', 'disabled_at') && $user->disabled_at !== null) {
            Auth::logout();

            if ($request->hasSession()) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Dieses Konto ist deaktiviert.',
                ], 403);
            }

            return redirect()->route('login')->withErrors([
                'email' => 'Dieses Konto ist deaktiviert.',
            ]);
        }

        return $next($request);
    }
}
