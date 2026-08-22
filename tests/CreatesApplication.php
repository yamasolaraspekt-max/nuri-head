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
        $db = TestDatenbank::name(getenv('TEST_ROLLE') ?: null);
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
