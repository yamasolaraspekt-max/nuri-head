<?php

namespace App\Console\Commands;

use App\Models\LeadStage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Leads-Kanban Stufe A: Backfill von lead_product_lists.lead_stage_id aus dem
 * bestehenden String-`status`. Idempotent (mehrfach ausfuehrbar, setzt denselben Wert).
 *
 * Mapping (status -> ziel-lead_stages.key):
 *   lead->lead, offer->offer, deal->deal, project->project  (unveraendert)
 *   accepted -> deal    (Zusage = Auftrag)
 *   follow_up -> offer  (Angebot; Zustand "Wiedervorlage" bleibt implizit in status='follow_up')
 * Legacy `status` wird NICHT angefasst.
 *
 *   php artisan leads:backfill-lead-stage-id            # backfuellen
 *   php artisan leads:backfill-lead-stage-id --rollback # alle lead_stage_id auf null
 */
class BackfillLeadStageId extends Command
{
    protected $signature = 'leads:backfill-lead-stage-id {--rollback : Setzt lead_stage_id aller Gewerke zurueck auf null}';

    protected $description = 'Stufe A: lead_product_lists.lead_stage_id aus status backfuellen (idempotent; --rollback nullt alle).';

    /** Umklassifizierung: status-key, dessen Ziel-Phase abweicht. */
    private const RECLASS = [
        'accepted'  => 'deal',
        'follow_up' => 'offer',
    ];

    /** Nicht-kanonische status-Werte auf die Lead-Phase normalisieren. */
    private const LEAD_ALIASES = ['', 'open', 'new', 'neu', 'neue'];

    public function handle(): int
    {
        if (!Schema::hasColumn('lead_product_lists', 'lead_stage_id')) {
            $this->error('Spalte lead_product_lists.lead_stage_id fehlt - Migration zuerst ausfuehren.');
            return self::FAILURE;
        }

        if ($this->option('rollback')) {
            $n = DB::table('lead_product_lists')->whereNotNull('lead_stage_id')->update(['lead_stage_id' => null]);
            $this->info("Rollback: lead_stage_id auf null gesetzt ($n Zeilen).");
            return self::SUCCESS;
        }

        $idByKey = LeadStage::query()->pluck('id', 'key')->toArray();

        $rows = DB::table('lead_product_lists')->whereNull('deleted_at')->get(['id', 'status']);

        $counts = [];
        $unknown = [];
        $updated = 0;

        foreach ($rows as $r) {
            $status = strtolower(trim((string) ($r->status ?? '')));
            $canon = in_array($status, self::LEAD_ALIASES, true) ? 'lead' : $status;
            $target = self::RECLASS[$canon] ?? $canon;

            $stageId = $idByKey[$target] ?? null;

            if ($stageId === null) {
                $unknown[$status] = ($unknown[$status] ?? 0) + 1;
                continue;
            }

            DB::table('lead_product_lists')->where('id', $r->id)->update(['lead_stage_id' => $stageId]);
            $updated++;
            $label = ($status === $target) ? $status : "$status -> $target";
            $counts[$label] = ($counts[$label] ?? 0) + 1;
        }

        $this->info("Backfill fertig: $updated Gewerke gebunden.");
        foreach ($counts as $label => $n) {
            $this->line(sprintf('  %-22s %d', $label, $n));
        }

        if (!empty($unknown)) {
            $this->warn('Unbekannte status-Werte (kein lead_stages.key) - lead_stage_id blieb null:');
            foreach ($unknown as $status => $n) {
                $this->line(sprintf('  %-22s %d', $status === '' ? '(leer)' : $status, $n));
            }
        }

        return self::SUCCESS;
    }
}
