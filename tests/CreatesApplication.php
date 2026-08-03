<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;

trait CreatesApplication
{
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

        return $app;
    }
}
