<?php

namespace App\Console\Commands;

use App\Services\Spec\SpecImportService;
use Illuminate\Console\Command;

/**
 * Rückbau eines spec:import-Batches über alle berührten Tabellen (products + die 4 Spec-Ziele).
 * Lehnt Update-Batches ab — überschriebene Vorwerte sind nicht wiederherstellbar (keine Schatten-Historie).
 */
class SpecRollbackCommand extends Command
{
    protected $signature = 'spec:rollback {batch_id : die import_batch_id (uuid) des Laufs}';

    protected $description = 'Rückbau eines spec:import-Batches (nur reine Insert-Läufe; Update-Batches werden abgelehnt).';

    public function handle(SpecImportService $svc): int
    {
        $r = $svc->rollback((string) $this->argument('batch_id'));

        if ($r['status'] === 'ok') {
            $summe = array_sum($r['geloescht']);
            $this->info("Batch zurückgebaut — {$summe} Zeilen gelöscht: ".json_encode($r['geloescht']));

            return self::SUCCESS;
        }

        $this->error($r['meldung']);

        return self::FAILURE;
    }
}
