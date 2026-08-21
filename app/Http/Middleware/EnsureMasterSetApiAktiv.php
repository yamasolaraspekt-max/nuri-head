<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Z2-W0-10 · Reversible Stilllegung von `api/secure/master-sets*` (Y-11, Yama 21.08.2026).
 *
 * **Der Befund:** die Schnittstelle liefert Einkaufspreise, Margen, Skonto, Händlerpreise,
 * Stundensätze, Klarnamen und Foto-URLs — und es liess sich **kein Konsument** finden
 * (`grep` über Repo und Doku fand nur Controller, Route und Beschreibung). Eine offene Fläche mit
 * genau diesen Daten und ohne bekannten Nutzer ist ein Risiko ohne Gegenwert.
 *
 * **Warum 404 und nicht 403:** ein 403 verrät, dass es die Schnittstelle gibt. Bei einer
 * stillgelegten Fläche ist das die einzige Auskunft, die noch etwas wert wäre — also gibt es sie
 * nicht. Die Antwort ist unabhängig von mitgeschickten Zugangsdaten; wer richtige Daten hat,
 * erfährt daraus nichts.
 *
 * **Warum Middleware und nicht Löschen:** Y-11 hat ausdrücklich die reversible Stilllegung gewählt.
 * Controller und Routen bleiben vollständig erhalten; der Rückweg ist `MASTER_SET_API_AKTIV=true`.
 * Taucht doch ein Konsument auf, ist er in einer Minute wieder bedient — und sein Auftauchen ist
 * zugleich die Antwort auf die Frage, die Y-11 offen nennt.
 */
class EnsureMasterSetApiAktiv
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless((bool) config('services.master_set_api.aktiv', false), 404);

        return $next($request);
    }
}
