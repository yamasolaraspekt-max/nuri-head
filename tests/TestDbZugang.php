<?php

namespace Tests;

/**
 * **Z0-I1-12 — der Verbindungsweg kommt aus einer Quelle, die jeder Baum hat.**
 *
 * ---
 *
 * **Die Rot-Lage, vom Evaluator gemessen:** zwei von fünf Bäumen haben weder `.env` noch
 * `.env.testing`. Host und Port stehen seit Z0-I1-12 versioniert in `phpunit.xml` und **wirken**
 * — kein `Connection refused`, kein Rückfall auf `3306`. Was fehlt, ist der **Benutzer**:
 * `config/database.php:51` fällt ohne `DB_USERNAME` auf `'forge'`, und der existiert nicht.
 *
 * ## Warum der Benutzername versioniert wird und das Kennwort nicht
 *
 * Das Blatt sagt es wörtlich: *„Zugangsdaten bleiben ausdrücklich unversioniert. Verlangt ist der
 * WEG, nicht das Kennwort."* Ein **Benutzername ist kein Geheimnis** — er steht in derselben
 * Klasse wie Host, Port und Datenbankname, die längst in `phpunit.xml` stehen. Das **Kennwort ist
 * eines** (gemessen: gesetzt, neun Zeichen) und bleibt draußen.
 *
 * ## Warum EINE Datei daneben und nicht vier Kopien darin
 *
 * Die Absage-Regel des Blattes trifft das Kopieren: *„Die Datei in die zwei Bäume kopieren erfüllt
 * (12) nicht … kopieren erzeugt vier Kopien, die auseinanderlaufen."* **Genau deshalb liegt die
 * Quelle außerhalb aller Bäume** — an derselben Stelle, an der schon die Leases liegen und auf die
 * `phpunit.xml` mit `TESTDB_LEASE_WURZEL` bereits zeigt. *Ein Baum mehr ändert nichts; die Quelle
 * bleibt eine.*
 *
 * ```text
 * versioniert, geheimnisfrei    phpunit.xml:  DB_USERNAME, TESTDB_ZUGANG (der ZEIGER)
 * unversioniert, einmalig       ${HOME}/.ticket-steuerung/testdb-zugang.env  (das KENNWORT)
 * ```
 *
 * **Vorrang:** eine bereits gesetzte Umgebung gewinnt. Wer `DB_PASSWORD` selbst setzt — etwa ein
 * Baum mit eigener `.env` —, wird hier nicht überschrieben. *Diese Klasse füllt eine Lücke, sie
 * erzwingt nichts.*
 */
final class TestDbZugang
{
    /** Die Datei wird EINMAL je Prozess gelesen. */
    private static bool $gelesen = false;

    /**
     * Legt fehlende Zugangswerte aus der gemeinsamen Quelle nach.
     *
     * Läuft **vor** dem Bootstrap — `config()` gibt es hier noch nicht, deshalb `getenv`/`putenv`
     * und kein Container.
     */
    public static function herstellen(): void
    {
        if (self::$gelesen) {
            return;
        }
        self::$gelesen = true;

        // Schon vorhanden? Dann ist nichts nachzulegen — ein Baum mit eigener `.env` bleibt
        // unangetastet. **Kein Ueberschreiben**, sonst waere diese Klasse eine zweite Wahrheit.
        if (self::gesetzt('DB_PASSWORD') && self::gesetzt('DB_USERNAME')) {
            return;
        }

        $pfad = self::pfad();
        if ($pfad === '' || ! is_file($pfad)) {
            // **Kein Wurf.** Ein Baum MIT `.env` läuft weiter wie bisher; nur die Bäume ohne
            // brauchen die Quelle. Fehlt sie dort, scheitert die Verbindung mit der Meldung der
            // Datenbank — und die ist aussagekräftiger als ein Abbruch an dieser Stelle.
            return;
        }

        foreach (self::zeilen($pfad) as $schluessel => $wert) {
            if (! self::gesetzt($schluessel)) {
                putenv($schluessel.'='.$wert);
                $_ENV[$schluessel] = $wert;
                $_SERVER[$schluessel] = $wert;
            }
        }
    }

    /** Der Zeiger aus der versionierten Quelle. `${HOME}`/`~` werden HIER aufgelöst, nicht in der XML. */
    public static function pfad(): string
    {
        $p = getenv('TESTDB_ZUGANG');
        if (! is_string($p) || $p === '') {
            return '';
        }
        $heim = (string) (getenv('HOME') ?: '');
        $p = str_replace(['${HOME}', '$HOME'], $heim, $p);
        if (str_starts_with($p, '~/')) {
            $p = $heim.substr($p, 1);
        }

        return $p;
    }

    /** Gesetzt heisst: vorhanden UND nicht leer. `getenv` gibt `false` oder `''` zurueck. */
    private static function gesetzt(string $schluessel): bool
    {
        $w = getenv($schluessel);

        return is_string($w) && $w !== '';
    }

    /**
     * `SCHLUESSEL=wert` je Zeile. **Kein `parse_ini_file`** — es scheitert an Sonderzeichen in
     * Kennwörtern (an der `.env` dieses Repos gemessen: „syntax error, unexpected '&'").
     * Kommentare und Leerzeilen werden übergangen; Anführungszeichen fallen weg.
     */
    private static function zeilen(string $pfad): array
    {
        $aus = [];
        foreach (file($pfad, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $zeile) {
            $zeile = trim($zeile);
            if ($zeile === '' || str_starts_with($zeile, '#')) {
                continue;
            }
            $teile = explode('=', $zeile, 2);
            if (count($teile) !== 2) {
                continue;
            }
            $aus[trim($teile[0])] = trim($teile[1], " \t\"'");
        }

        return $aus;
    }
}
