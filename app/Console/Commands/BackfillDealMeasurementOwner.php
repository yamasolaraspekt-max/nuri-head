<?php

namespace App\Console\Commands;

use App\Models\DealMeasurement;
use Illuminate\Console\Command;

/**
 * S-1a Altdaten-Backfill: füllt fehlende deal_measurements.created_by aus deals.employee_id
 * (Deal-Zuständiger als Owner-Fallback), damit DealMeasurementPolicy Altdaten zuordnen kann.
 * Rest-Waisen (auch kein deal.employee_id) bleiben Waisen → weiches Deny greift bis Umschaltung.
 *
 * Produktiver Lauf = Teil der M5-/Deploy-Freigabe (NICHT im Feature-Commit ausgeführt).
 */
class BackfillDealMeasurementOwner extends Command
{
    protected $signature = 'deal-measurements:backfill-owner {--dry-run : Nur zählen, nichts schreiben}';

    protected $description = 'S-1a: füllt fehlende created_by der Aufmaße aus deals.employee_id (Owner-Backfill).';

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');
        $filled = 0;
        $orphan = 0;

        DealMeasurement::whereNull('created_by')->with('deal')->chunkById(200, function ($chunk) use (&$filled, &$orphan, $dry) {
            foreach ($chunk as $m) {
                $owner = $m->deal?->employee_id;
                if ($owner) {
                    if (! $dry) {
                        $m->update(['created_by' => $owner]);
                    }
                    $filled++;
                } else {
                    $orphan++;
                }
            }
        });

        $this->info("Owner-Backfill: backfillbar={$filled} · Rest-Waisen={$orphan}" . ($dry ? ' [dry-run]' : ''));

        return self::SUCCESS;
    }
}
