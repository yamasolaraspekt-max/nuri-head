<?php

namespace Tests;

use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * **Z0-I1-1/-2/-10 — der Riegel fragt die VERBINDUNG, nicht die Konfiguration.**
 *
 * ---
 *
 * **Der Anlass, und er ist gemessen und nicht ausgedacht:** parallele Läufe setzten dieselbe
 * `ticket_testing` gegenseitig zurück; ein Testkonto verschwand *während* einer laufenden
 * Browserabnahme (Evaluator-Bericht, Gesamtauftrag v2 Phase 3).
 *
 * **Warum `SELECT DATABASE()` und nicht `config('database…')`:** *Was konfiguriert ist, ist eine
 * Absicht; was die Datenbank antwortet, ist die Tatsache.* Die beiden sind in diesem Haus schon
 * auseinandergelaufen — das Errata vom 22.08. zeigt den Fall: eine Rechteliste sagte „kein
 * Zugriff", der Zugriff gelang trotzdem. **Eine Rechteliste ist kein Zugriffsbeleg**, und eine
 * Konfigurationszeile ist kein Verbindungsbeleg.
 *
 * **Wo er läuft:** in `CreatesApplication::createApplication()`, unmittelbar nach dem Bootstrap —
 * dort steht die Verbindung, und `RefreshDatabase` mit seiner Migration kommt erst danach.
 * *Ein Riegel hinter dem ersten Schreibzugriff hätte den Schaden schon zugelassen.*
 *
 * ## Stufe 1 kennt GENAU EINEN Namen
 *
 * `ticket_testing`, exakt verglichen. **Kein Präfix-Muster** — `ticket_testing_kopie` trägt
 * dieselben Daten wie das Original und bleibt abgewiesen; `zz_ticket_testing` ebenso.
 * *Ein Muster wie `ticket_testing*` ließe beide durch.* Die vier Rollen-Datenbanken der Stufe 2
 * kommen mit `root` und einem eigenen Blatt; bis dahin ist jeder andere Name ein Abbruch.
 *
 * **Fail closed:** keine Auskunft heißt Abbruch, nicht „vermutlich Test". Die Meldung nennt den
 * **gefundenen** Namen — wer nur „falsche Datenbank" liest, weiß nicht, welche er getroffen hat.
 */
final class TestDatenbankGuard
{
    /** Der einzige in Stufe 1 zulässige Datenbankname. Exakt, nicht als Muster. */
    public const ERLAUBT = 'ticket_testing';

    /**
     * Fragt die Verbindung und bricht ab, wenn sie nicht auf der erlaubten Datenbank steht.
     *
     * @return string der tatsächlich verbundene Name — damit ihn der Aufrufer belegen kann (Z0-I1-10)
     */
    public static function pruefeVerbindung(): string
    {
        try {
            $zeile = DB::selectOne('SELECT DATABASE() AS db');
        } catch (\Throwable $e) {
            // Keine Auskunft ist kein Freibrief. Der ursprüngliche Fehler geht mit — ohne ihn
            // sucht der Nächste an der falschen Stelle.
            throw new RuntimeException(
                'Z0-I1-1: Die Datenbank gab auf SELECT DATABASE() keine Auskunft. Kein Lauf, '
                .'nichts geschrieben. Ursprung: '.$e->getMessage(),
                0,
                $e,
            );
        }

        $gefunden = is_object($zeile) && isset($zeile->db) ? (string) $zeile->db : '';

        if ($gefunden === '') {
            throw new RuntimeException(
                'Z0-I1-1: SELECT DATABASE() lieferte einen LEEREN Namen — die Verbindung steht auf '
                .'keiner Datenbank. Kein Lauf, nichts geschrieben.',
            );
        }

        if ($gefunden !== self::ERLAUBT) {
            throw new RuntimeException(
                "Z0-I1-2: Der Testlauf ist mit der Datenbank '{$gefunden}' verbunden, erlaubt ist "
                ."ausschliesslich '".self::ERLAUBT."'. ABBRUCH VOR dem ersten Schreibzugriff — "
                .'keine Migration, kein Seed, kein Truncate.',
            );
        }

        return $gefunden;
    }
}
