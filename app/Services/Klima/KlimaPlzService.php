<?php

namespace App\Services\Klima;

use App\Models\KlimaPlz;
use Illuminate\Support\Str;

/**
 * KlimaPlzService
 *
 * Entscheidung: Interne Datenquelle -> DB (Tabelle `klima_plz`).
 * Muster: C1 — "Eine Wahrheit schlägt Diff=0" (keine API- oder Callerseitigen
 * Änderungen). Die Service-Public-API bleibt unverändert: `findByPlz()`
 * und `getNormAussentempForPlz()` liefern dieselben Werte wie vorher.
 *
 * Hintergrund: vorherige Implementationen nutzten die CSV in
 * `database/data/klima_plz.csv` als Quelle (nur Seeder/Referenzdaten).
 * Die Laufzeit-Lookups erfolgen nun per Eloquent-Query gegen die
 * Tabelle `klima_plz` (erhöhte Wartbarkeit, Indexe, Query-Optimierung).
 *
 * Dokumentation: docs/entscheidungen.md — Eintrag 'KLIMA-PLZ-DB-SOURCE'.
 */
class KlimaPlzService
{
    public function findByPlz(string $plz): ?KlimaPlz
    {
        $plz = Str::of($plz)->trim();

        if ($plz->isEmpty() || $plz->length() < 5) {
            return null;
        }

        return KlimaPlz::where('plz', $plz->toString())->first();
    }

    public function getNormAussentempForPlz(string $plz): ?float
    {
        $klima = $this->findByPlz($plz);

        return $klima ? $klima->nat_c : null;
    }
}
