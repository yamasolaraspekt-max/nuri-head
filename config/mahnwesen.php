<?php

/*
|--------------------------------------------------------------------------
| Mahnwesen — Standard-Regeln (Welle A1, Paket 2 · 2026-07-16)
|--------------------------------------------------------------------------
| Von Yama freigegeben als "Standard-Regeln" (übliche deutsche Mahnpraxis).
| EINE Wahrheit für alle Mahn-Parameter: Änderungen NUR hier, nie im Code.
| Verzugszinsen (§ 288 BGB: B2C 5, B2B 9 %-Punkte über Basiszins) werden in
| V1 NICHT automatisch berechnet (Basiszins ändert sich halbjährlich) —
| das Schreiben weist auf sie hin; Berechnung ist eine spätere Ausbaustufe.
*/

return [

    // Tage nach Fälligkeit, bevor eine Rechnung überhaupt Mahn-Kandidat wird.
    'karenz_tage' => 7,

    // Mindestabstand zwischen zwei Mahnstufen derselben Rechnung (Tage).
    'stufen_abstand_tage' => 14,

    // Die drei Stufen: Bezeichnung, Gebühr (EUR), Zahlungsfrist im Schreiben (Tage).
    'stufen' => [
        1 => ['titel' => 'Zahlungserinnerung', 'gebuehr' => 0.00,  'frist_tage' => 14],
        2 => ['titel' => '1. Mahnung',         'gebuehr' => 5.00,  'frist_tage' => 14],
        3 => ['titel' => '2. Mahnung (letzte Mahnung)', 'gebuehr' => 10.00, 'frist_tage' => 10],
    ],

    // Hinweistext Verzugszinsen (erscheint ab Stufe 2 im Schreiben).
    'zins_hinweis' => 'Wir behalten uns vor, Verzugszinsen gemäß § 288 BGB '
        . '(5 Prozentpunkte über dem Basiszinssatz, bei Geschäften zwischen Unternehmen '
        . '9 Prozentpunkte) sowie weitere Verzugsschäden geltend zu machen.',

    // Schlusstext der letzten Mahnstufe.
    'stufe3_hinweis' => 'Sollte auch diese Frist ergebnislos verstreichen, werden wir die '
        . 'Forderung ohne weitere Ankündigung an ein Inkassounternehmen übergeben bzw. das '
        . 'gerichtliche Mahnverfahren einleiten. Die dadurch entstehenden Kosten gehen zu Ihren Lasten.',
];
