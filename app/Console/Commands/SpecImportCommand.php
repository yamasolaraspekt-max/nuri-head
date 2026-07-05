<?php

namespace App\Console\Commands;

use App\Services\Spec\SpecImportService;
use App\Services\Spec\SpecSchema;
use Illuminate\Console\Command;

/**
 * Geräte-Spec-Import nach dem Spec-Standard (docs/spec-import/00-spec-standard.md §4).
 * Baustufe 1: DEFAULT ist DRY-RUN (Validierung + Report, KEIN Write). --commit/--update = Baustufe 2.
 */
class SpecImportCommand extends Command
{
    protected $signature = 'spec:import {datei : Pfad zu JSON (kanonisch) oder CSV (Zubringer)}
        {--typ= : Gerätetyp (sonst aus geraetetyp im JSON)}
        {--commit : schreiben statt Dry-Run (Baustufe 2)}
        {--update : Bestand aktualisieren (Baustufe 2)}
        {--allow-downgrade : verifiziert->ungeprueft erlauben (Baustufe 2)}';

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

        $report = $svc->dryRun((string) $typ, $geraete);
        $this->line("Typ: <info>{$typ}</info> · ".count($geraete).' Geräte gelesen');
        $this->line(sprintf('  angelegt: %d · geskippt (Dedup): %d · abgelehnt: %d',
            count($report['angelegt']), count($report['geskippt']), count($report['abgelehnt'])));
        foreach ($report['abgelehnt'] as $ab) {
            $this->line("  <fg=red>✗ {$ab['id']}</>: ".implode(' | ', $ab['fehler']));
        }

        if ($this->option('commit') || $this->option('update')) {
            $this->warn('--commit/--update ist noch nicht implementiert (Baustufe 2). Es wurde NICHTS geschrieben.');
        } else {
            $this->info('DRY-RUN — nichts geschrieben. Mit --commit schreiben (ab Baustufe 2).');
        }

        return $report['abgelehnt'] === [] ? self::SUCCESS : self::FAILURE;
    }
}
