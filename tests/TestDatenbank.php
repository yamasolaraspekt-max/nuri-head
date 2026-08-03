<?php

namespace Tests;

use RuntimeException;

/**
 * **Welche Datenbank ein Testlauf benutzt — als Funktion, nicht als Umgebungsvertrauen.**
 *
 * Anlass (PB-056, 03.08.): drei Rollen fuhren gleichzeitig `RefreshDatabase` gegen dieselbe
 * `ticket_testing`. Ergebnis waren Deadlocks beim `drop table` und **falsche rote Ergebnisse** —
 * `8 failed, 0 assertions in 1,6 s`, ein Lauf, der vor der ersten Zusicherung stirbt. *Wer diese
 * Zahl ohne Dauer und Zusicherungszahl liest, meldet einen Bau-Fehler, den es nicht gibt.*
 *
 * **Die Entscheidung steht hier und nicht im Boot-Punkt**, damit sie geprueft werden kann, ohne
 * einen Lauf abzubrechen (B3: gemessen wird an der entscheidenden Funktion, nicht am Ausfuehrer).
 *
 * **Zwei Riegel, nicht einer:**
 *  1. die Rolle muss auf der Liste stehen — kein Vertrauen in die Umgebung,
 *  2. der ERGEBNISNAME muss auf das harte Praefix passen.
 *
 * *Der zweite ist nicht ueberfluessig: waere die Liste eines Tages offener, faengt er trotzdem
 * `../produktion` und jeden Namen, der aus dem Testbereich hinausfuehrt.* **Ein Riegel, der nur
 * die Eingabe prueft, schuetzt so lange, bis jemand die Eingabe erweitert.**
 */
final class TestDatenbank
{
    /** Die Rollen des Zyklus. Wer keine ist, bekommt keine eigene Datenbank. */
    public const ROLLEN = ['generator', 'evaluator', 'planner', 'pruefer'];

    /** Der Name ohne Rolle — der gemeinsame Schreibtisch, und die Vorgabe. */
    public const BASIS = 'ticket_testing';

    /**
     * Liefert den Datenbanknamen fuer diese Rolle.
     *
     * Ohne `TEST_ROLLE` bleibt es bei `ticket_testing` — **der Bestand aendert sich nicht**, und
     * niemand muss etwas setzen, damit seine Laeufe weiterlaufen. Die Trennung ist ein Angebot,
     * keine neue Pflicht.
     */
    public static function name(?string $rolle): string
    {
        $rolle = trim((string) $rolle);
        if ($rolle === '') {
            return self::BASIS;
        }

        if (! in_array($rolle, self::ROLLEN, true)) {
            throw new RuntimeException(
                "TEST_ROLLE='{$rolle}' ist keine bekannte Rolle. Erlaubt: ".implode(', ', self::ROLLEN)
                .'. Kein Lauf, keine Datenbank angefasst.',
            );
        }

        $name = self::BASIS.'_'.$rolle;

        // Zweiter Riegel — am ERGEBNIS, nicht an der Eingabe.
        if (preg_match('/^'.self::BASIS.'_[a-z]+$/', $name) !== 1) {
            throw new RuntimeException(
                "Der Datenbankname '{$name}' passt nicht auf das harte Praefix '".self::BASIS."_'. Kein Lauf.",
            );
        }

        return $name;
    }
}
