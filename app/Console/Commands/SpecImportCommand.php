<?php

namespace App\Console\Commands;

use App\Services\Spec\SpecImportService;
use App\Services\Spec\SpecSchema;
use Illuminate\Console\Command;

/**
 * Geräte-Spec-Import nach dem Spec-Standard (docs/spec-import/00-spec-standard.md §4).
 * DEFAULT Dry-Run (kein Write). --commit schreibt mit Herkunftskette; --update mit Felddiff + Downgrade-Schutz.
 */
class SpecImportCommand extends Command
{
    protected $signature = 'spec:import {datei : Pfad zu JSON (kanonisch) oder CSV (Zubringer)}
        {--typ= : Gerätetyp (sonst aus geraetetyp im JSON)}
        {--quelle=spec:import : imported_from-Marker der geschriebenen Zeilen}
        {--commit : schreiben statt Dry-Run}
        {--update : bestehende (brand+model) aktualisieren statt überspringen}
        {--allow-downgrade : verifiziert->ungeprueft überschreiben erlauben}';

    protected $description = 'Importiert Geräte-Specs (JSON/CSV) nach dem Spec-Standard. Default: DRY-RUN (kein Write).';

    public function handle(SpecImportService $svc): int
    {
        $datei = (string) $this->argument('datei');
        if (! is_file($datei)) {
            $this->error("Datei nicht gefunden: {$datei}");

            return self::FAILURE;
        }
        try {
            $geraete = $svc->parse($datei);
        } catch (\JsonException $ex) {
            $this->error('JSON nicht lesbar: '.$ex->getMessage());

            return self::FAILURE;
        }
        $typ = $this->option('typ') ?: ($geraete[0]['geraetetyp'] ?? null);
        if (! SpecSchema::isType((string) $typ)) {
            $this->error('Gerätetyp unbekannt/fehlt: '.var_export($typ, true).' (erlaubt: '.implode(', ', SpecSchema::types()).')');

            return self::FAILURE;
        }

        if ($this->option('commit')) {
            return $this->commitReport($svc->commit((string) $typ, $geraete, [
                'imported_from' => (string) $this->option('quelle'),
                'update' => (bool) $this->option('update'),
                'allow_downgrade' => (bool) $this->option('allow-downgrade'),
            ]));
        }

        $report = $svc->dryRun((string) $typ, $geraete);
        $this->line("Typ: <info>{$typ}</info> · ".count($geraete).' Geräte gelesen');
        $this->line(sprintf('  angelegt: %d · geskippt (Dedup): %d · abgelehnt: %d',
            count($report['angelegt']), count($report['geskippt']), count($report['abgelehnt'])));
        foreach ($report['abgelehnt'] as $ab) {
            $this->line("  <fg=red>✗ {$ab['id']}</>: ".implode(' | ', $ab['fehler']));
        }
        $this->info('DRY-RUN — nichts geschrieben. Mit --commit schreiben.');

        return $report['abgelehnt'] === [] ? self::SUCCESS : self::FAILURE;
    }

    private function commitReport(array $r): int
    {
        if ($r['batchId'] === null) {
            $this->error($r['abbruch']);
            foreach ($r['abgelehnt'] as $ab) {
                $this->line("  <fg=red>✗ {$ab['id']}</>: ".implode(' | ', $ab['fehler']));
            }

            return self::FAILURE;
        }
        $this->info("Batch {$r['batchId']} geschrieben");
        $this->line(sprintf('  angelegt: %d · aktualisiert: %d · geskippt: %d · downgrade-abbruch: %d',
            count($r['angelegt']), count($r['aktualisiert']), count($r['geskippt']), count($r['downgradeAbbruch'])));
        foreach ($r['diffs'] as $id => $diff) {
            $this->line("  ~ {$id}:");
            foreach ($diff as $feld => [$alt, $neu]) {
                $this->line("      {$feld}: ".var_export($alt, true).' -> '.var_export($neu, true));
            }
        }
        foreach ($r['downgradeAbbruch'] as $id) {
            $this->warn("  ⚠ {$id}: datenblatt_verifiziert — nicht überschrieben (--allow-downgrade nötig)");
        }

        return self::SUCCESS;
    }
}
