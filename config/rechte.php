<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Alle Rechte für alle Nutzer
    |--------------------------------------------------------------------------
    |
    | Yamas Entscheidung vom 21.08.2026, zweimal bestätigt und dokumentiert in
    | `docs/regelwerk/ENTSCHEIDUNG-RECHTE-ALLE-FUER-ALLE.md`.
    |
    | Steht dieser Schalter auf `true`, gilt JEDE Rechteprüfung als bestanden —
    | `User::hasPermission()` liefert unbesehen `true`, und damit auch die
    | `permission:`-Middleware, die Blade-Sichtbarkeit und jeder andere Aufrufer.
    |
    | **Warum ein Schalter und keine Datenmutation:** der Rückweg ist eine Zeile.
    | Auf `false` wirken die vorhandenen Tore sofort wieder; es gibt keine 52×N
    | `user_rolls`-Zeilen, die jemand zurückbauen müsste, und die Entscheidung
    | steht an genau EINER Stelle im Code statt verteilt in Datenzeilen, die in
    | drei Monaten niemand mehr einer Entscheidung zuordnet.
    |
    | **Was er NICHT tut:** er macht niemanden zum Admin. `isSuperAdmin()` bleibt
    | unberührt — die Sonderpfade der Nutzerverwaltung gehören weiter den Admins.
    |
    | Der Vorgabewert ist `false`. Die Testumgebung MUSS auf `false` laufen, sonst
    | prüft kein Tor-Test mehr, ob ein Tor überhaupt schließt; der `true`-Fall
    | wird je Test ausdrücklich gesetzt.
    |
    */

    'alle_fuer_alle' => env('RECHTE_ALLE_FUER_ALLE', false),

];
