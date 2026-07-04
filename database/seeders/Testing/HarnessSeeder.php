<?php

namespace Database\Seeders\Testing;

use Illuminate\Database\Seeder;

/**
 * Orchestrator des [TEST-HARNESS]. Legt den kompletten Test-Kontext + Szenarien an.
 * Idempotent (mehrfaches Ausfuehren dupliziert nicht). Ausschliesslich local.
 *
 *   php artisan db:seed --class="Database\Seeders\Testing\HarnessSeeder"
 *
 * Abraeumen mit dem separaten Teardown:
 *   php artisan db:seed --class="Database\Seeders\Testing\HarnessTeardownSeeder"
 */
class HarnessSeeder extends Seeder
{
    use HarnessSupport;

    public function run(): void
    {
        $this->guardLocal();

        $this->call([
            HarnessContextSeeder::class,
            QualifikationTestSeeder::class,
            MontageTestSeeder::class,
            KanbanTestSeeder::class,
        ]);

        $this->command?->info(self::TAG . ' Harness vollstaendig geseedet.');
    }
}
