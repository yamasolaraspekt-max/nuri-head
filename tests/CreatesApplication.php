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

        return $app;
    }
}
