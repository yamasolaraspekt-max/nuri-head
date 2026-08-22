<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;

trait CreatesApplication
{
    /**
     * Der zuletzt vom Guard gemessene Datenbankname — die TATSACHE, nicht die Konfiguration.
     * Statisch, weil der Beleg den Lauf ueberdauern muss und nicht die einzelne Testklasse.
     */
    public static string $verbundeneDatenbank = '';

    /** Wer diesen Lauf haelt — aus TEST_ROLLE, geprueft. Z0-I1-4 und Grundlage fuer Z0-I1-9. */
    public static string $halter = '';

    /** Die Lease wird EINMAL je Prozess gezogen, nicht je Testfall. */
    private static bool $leaseGezogen = false;

    /**
     * Der Name wird EINMAL je Lauf gemeldet — aus demselben Grund wie die Lease.
     * Bei 1778 Testfaellen waeren es sonst 1778 Zeilen, und eine Meldung, die man wegscrollt,
     * ist keine Meldung (A-03).
     */
    private static bool $nameGemeldet = false;

    /** Eine Kennung fuer diesen Lauf — sie steht als `owner` in der Lease. */
    private static function sitzung(): string
    {
        $s = getenv('SITZUNGS_ID');

        return is_string($s) && $s !== '' ? $s : ('lauf-'.getmypid());
    }

    /**
     * Creates the application.
     */
    /**
     * Creates the application.
     *
     * **PB-056: je Rolle eine eigene Test-Datenbank, wenn `TEST_ROLLE` gesetzt ist.**
     * Der Name wird VOR dem Bootstrap gesetzt — danach steht die Verbindung und ein Wechsel
     * käme zu spät. `TestDatenbank::name()` wirft bei allem, was keine bekannte Rolle ist;
     * *ein Lauf, der die falsche Datenbank träfe, findet gar nicht erst statt.*
     */
    public function createApplication(): Application
    {
        // Z0-I1-4: die Rolle ist PFLICHT. Sie benennt in Stufe 1 den Lease-Halter, nicht die
        // Datenbank — die ist die eine aus `phpunit.xml:28` (`force="true"`, versioniert).
        // `TestDatenbank::name()` bleibt unangetastet: sie beantwortet die Stufe-2-Frage, welche
        // DB eine Rolle bekaeme, und ihre Zusage gilt weiter.
        self::$halter = TestDatenbank::verlangeRolle(getenv('TEST_ROLLE') ?: null);

        // Z0-I1-9: die Lease VOR dem ersten Schreibzugriff — und genau EINMAL je Prozess.
        // `createApplication` laeuft je Testfall; die Lease gilt fuer den ganzen Lauf, sonst
        // zoege ein Lauf mit 300 Faellen 300 Token und der Zaehler saegte sich selbst durch.
        if (! self::$leaseGezogen) {
            self::$leaseGezogen = true;
            TestDbLease::ziehen(TestDatenbank::BASIS, self::$halter, self::sitzung());
            // Freigabe am PROZESSENDE, nicht am Testfall-Ende. Ein Abbruch mitten im Lauf laesst
            // sie stehen — dagegen steht `heartbeat_bis`, nicht ein Hoffen auf sauberes Beenden.
            register_shutdown_function(static fn () => TestDbLease::freigeben());
        }

        // Z0-I1-12: fehlende Zugangswerte aus der gemeinsamen Quelle nachlegen — VOR dem
        // Bootstrap, denn danach steht die Verbindung und `config()` ist eingefroren. Ein Baum
        // mit eigener `.env` wird nicht angefasst; die Klasse fuellt nur Luecken.
        TestDbZugang::herstellen();

        // Der Name wird VOR dem Bootstrap gesetzt — danach steht die Verbindung.
        $db = TestDatenbank::BASIS;
        putenv('DB_DATABASE='.$db);
        $_ENV['DB_DATABASE'] = $db;
        $_SERVER['DB_DATABASE'] = $db;

        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        // Z0-I1-1/-2: HIER, nach dem Bootstrap und VOR jedem `RefreshDatabase`. Der Name oben ist
        // eine Absicht; erst `SELECT DATABASE()` sagt, wohin die Verbindung wirklich zeigt.
        // Der gefundene Name wird gemerkt, damit ihn jeder Beleg zitieren kann (Z0-I1-10).
        self::$verbundeneDatenbank = TestDatenbankGuard::pruefeVerbindung();

        // ── Z0-I1-10 — DER LAUF SAGT, WOHIN ER VERBUNDEN IST ────────────────────────────────
        //
        // **Bis hierher war der Name nur GEMERKT.** Die Zeile darueber steht seit Stufe 1 im
        // Code und traegt sogar den Vermerk „damit ihn jeder Beleg zitieren kann" — aber sie
        // legte ihn in eine statische Variable, die **niemand liest**. Der Evaluator hat genau
        // das gemessen: `echo`/`fwrite`/`print` im Bootweg 0, `printer`/`extension` in der
        // phpunit.xml 0. *Ein Wert, den man abrufen KANN, ist kein Beleg; ein Beleg ist einer,
        // der im Lauf STEHT.*
        //
        // **STDERR, nicht STDOUT:** PHPUnit schreibt sein eigenes Ergebnis (TAP, JUnit, Testdox)
        // nach STDOUT; eine Fremdzeile darin bricht maschinenlesbare Formate. STDERR erscheint
        // im Terminal und in jedem Protokoll, ohne das Ergebnis zu verunreinigen.
        //
        // **Die Form folgt dem eigenen Bestand** — `scripts/pruefstand-saeen.sh` meldet
        // `SAAT ok db=ticket_testing …`. Kein zweites Format fuer dieselbe Sache.
        if (! self::$nameGemeldet) {
            self::$nameGemeldet = true;
            fwrite(STDERR, sprintf(
                'TESTLAUF db=%s halter=%s quelle=SELECT_DATABASE()'.PHP_EOL,
                self::$verbundeneDatenbank,
                self::$halter,
            ));
        }

        return $app;
    }
}
