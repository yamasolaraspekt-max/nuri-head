<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'fusion/webhook/ajax',
        'ai/chats/*/message',
        // Z2-W0-11 Teil B (nach Y-12): der IDS-Rueckweg soll statt dieser Ausnahme einen
        // signierten State tragen. Bis dahin bleibt sie stehen — mit Teil A ist die
        // Fremdzuschreibung bereits weg (uid kommt aus der Sitzung, nicht aus der Query).
        'ids/callback',
        'offers/document/presence/leave',
        'admin/supplier-connectors/*/return',
        'admin/offers/folders/*/supplier/*/return',
    ];

    // ------------------------------------------------------------------------
    // Z2-W0-11 Teil A (21.08.2026) — fuenf tote Ausnahmen entfernt
    // ------------------------------------------------------------------------
    // Eine Ausnahme, die keine Route trifft, schuetzt niemanden und verdeckt nur, welche
    // Schreibpfade wirklich ohne CSRF laufen. Vor dem Entfernen einzeln gegen `route:list`
    // gemessen — nicht gelesen, gemessen:
    //
    //   'api/reminder/<*>/status' -> die Route heisst `reminder/{id}/status`, OHNE `api/`.
    //   'api/due-personal-notes'  -> die Route heisst `due-personal-notes`, OHNE `api/`.
    //   'ids/search/callback'     -> existiert nicht (es gibt `ids/search/forward`).
    //   '/ids/receive'            -> existiert nicht.
    //   '/ids/callback'           -> Dublette MIT fuehrendem Schraegstrich. `inExceptArray()`
    //                                prueft `fullUrlIs()` und `is()`; der Anfragepfad lautet
    //                                `ids/callback` ohne Schraegstrich, das Muster traf also nie.
    //                                Gewirkt hat immer der Eintrag darunter.
    //
    // (Das <*> oben steht fuer den Stern des echten Musters. Er darf hier nicht woertlich
    //  stehen: `*` gefolgt von `/` beendet einen Blockkommentar — der erste Anlauf dieser
    //  Notiz hat genau daran die Datei zerlegt. Deshalb Zeilenkommentare.)
    //
    // Alle sechs verbliebenen Eintraege treffen eine reale Route (gemessen 21.08.). Der Waechter
    // `tests/Feature/Security/IdsCallbackZuschreibungTest` haelt das fest und wird rot, sobald
    // jemand eine Ausnahme eintraegt, die ins Leere zeigt.
    //
    // KEIN Verhalten geaendert: die fuenf Muster haben nie gegriffen, ihr Wegfall kann folglich
    // keinen Schreibpfad brechen. Wer sie zurueckholen will, holt sich nur den Irrtum zurueck.
}
