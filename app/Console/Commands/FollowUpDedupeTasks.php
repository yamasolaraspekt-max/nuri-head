<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * A1b — Vorlauf zum Unique-Index auf personal_tasks(type, source_type, source_id).
 *
 * Erkennt und bereinigt PROTOKOLLIERT Dubletten je natürlichem Schlüssel
 * (type, source_type, source_id) — nur non-null Quellen (manuelle Tasks mit
 * source_type/source_id NULL bleiben unangetastet; MySQL erlaubt dort mehrere NULLs).
 *
 * Regel (DAUERDIREKTIVE Punkt 1): ZUERST zählen/listen, DANN bereinigen, DANN nachzählen.
 * Pro Schlüssel wird der ÄLTESTE (MIN(id)) behalten, jüngere Dubletten entfernt
 * (soft-delete via deleted_at, da personal_tasks SoftDeletes nutzt). Kein Blind-Delete.
 *
 * Muss VOR der Unique-Index-Migration laufen; die Migration verweigert sonst den Dienst.
 */
class FollowUpDedupeTasks extends Command
{
    protected $signature = 'followup:dedupe-tasks {--dry-run : Nur erkennen/zählen, nichts schreiben}';

    protected $description = 'A1b: erkennt + bereinigt Dubletten je (type, source_type, source_id) auf personal_tasks (ältester bleibt).';

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');
        $soft = Schema::hasColumn('personal_tasks', 'deleted_at');

        // 1) ERKENNEN (Vorher) — nur non-null Quellen, ohne bereits soft-gelöschte Zeilen
        $groups = $this->duplicateGroups();
        $vorher = $groups->sum(fn ($g) => (int) $g->c);
        $keys   = $groups->count();

        $this->info(sprintf(
            'DEDUP-VORHER: %d Dublett-Schlüssel, %d betroffene Zeilen (soft-delete=%s, dry-run=%s).',
            $keys,
            $vorher,
            $soft ? 'ja' : 'nein',
            $dry ? 'ja' : 'nein',
        ));

        if ($keys === 0) {
            $this->info('DEDUP: keine Dubletten — No-op.');
            $this->info('DEDUP-NACHHER: 0 Dublett-Schlüssel.');

            return self::SUCCESS;
        }

        // 2) LISTEN + BEREINIGEN — pro Schlüssel MIN(id) behalten, Rest entfernen
        $entfernt = 0;

        foreach ($groups as $g) {
            $rows = DB::table('personal_tasks')
                ->where('type', $g->type)
                ->where('source_type', $g->source_type)
                ->where('source_id', $g->source_id)
                ->whereNull('deleted_at')
                ->orderBy('id')
                ->pluck('id');

            $keep   = $rows->first();
            $remove = $rows->slice(1)->values();

            $this->line(sprintf(
                '  Schlüssel (%s, %s, %s): behalte id=%d, entferne %d [%s]',
                $g->type,
                $g->source_type,
                $g->source_id,
                $keep,
                $remove->count(),
                $remove->implode(','),
            ));

            if (! $dry && $remove->isNotEmpty()) {
                $q = DB::table('personal_tasks')->whereIn('id', $remove->all());
                if ($soft) {
                    $q->update(['deleted_at' => now()]);
                } else {
                    $q->delete();
                }
                $entfernt += $remove->count();
            }
        }

        // 3) NACHHER
        $nachher = $this->duplicateGroups()->sum(fn ($g) => (int) $g->c);
        $this->info(sprintf('DEDUP-ENTFERNT: %d Zeilen (%s).', $entfernt, $dry ? 'dry-run: nichts geschrieben' : 'geschrieben'));
        $this->info(sprintf('DEDUP-NACHHER: %d verbleibende Dublett-Zeilen.', $nachher));

        if (! $dry && $nachher > 0) {
            $this->error('DEDUP: nach Bereinigung noch Dubletten vorhanden — bitte prüfen.');

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    /** Dublett-Gruppen je (type, source_type, source_id), nur non-null, ohne soft-gelöschte. */
    private function duplicateGroups()
    {
        return collect(DB::table('personal_tasks')
            ->selectRaw('type, source_type, source_id, COUNT(*) AS c')
            ->whereNotNull('source_type')
            ->whereNotNull('source_id')
            ->whereNull('deleted_at')
            ->groupBy('type', 'source_type', 'source_id')
            ->havingRaw('COUNT(*) > 1')
            ->get());
    }
}
