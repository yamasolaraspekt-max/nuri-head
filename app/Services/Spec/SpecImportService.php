<?php

namespace App\Services\Spec;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Geräte-Spec-Import: Parsing (JSON kanonisch / CSV Zubringer) + Validierung gegen SpecSchema (V1–V7)
 * + Dry-Run-Report (Baustufe 1) + --commit-Pfad mit Herkunftskette/Downgrade-Schutz/Batch-Rückbau (Baustufe 2).
 *
 * docs/spec-import/00-spec-standard.md §3 (Regeln), §4 (Ablauf), §7/§8 (Weichen, Bau-Reihenfolge).
 */
class SpecImportService
{
    /** Alle von spec:import beschriebenen Tabellen (für den lauf-genauen Batch-Rückbau). */
    private const ZIEL_TABELLEN = ['product_heat_pump_specs', 'product_pv_module_specs', 'inverters', 'batteries'];

    /** @return list<array<string,mixed>> */
    public function parse(string $pfad): array
    {
        $ext = strtolower(pathinfo($pfad, PATHINFO_EXTENSION));
        $inhalt = (string) file_get_contents($pfad);
        if ($ext === 'csv') {
            return $this->csvZuKanonisch($inhalt);
        }
        $data = json_decode($inhalt, true, 512, JSON_THROW_ON_ERROR);
        if (isset($data['geraetetyp'])) {
            return [$data];
        }

        return array_values($data['geraete'] ?? $data);
    }

    /** @return list<string> Fehlerliste (leer = valide) */
    public function validate(string $typ, array $g): array
    {
        $def = SpecSchema::definition($typ);
        $fach = $g['fachdaten'] ?? [];
        $sem = $g['semantik'] ?? [];
        $herk = $g['herkunft'] ?? [];
        $e = [];

        foreach ($def['identitaet'] as $f) { // V5
            if (($g['identitaet'][$f] ?? null) === null || $g['identitaet'][$f] === '') {
                $e[] = "V5: identitaet.{$f} fehlt";
            }
        }
        foreach (array_keys($fach) as $f) { // V6
            if (! isset($def['fachdaten'][$f])) {
                $e[] = "V6: unbekanntes Feld fachdaten.{$f}";
            }
        }
        foreach (array_keys($sem) as $f) {
            if (! in_array($f, $def['semantik_erlaubt'] ?? [], true)) {
                $e[] = "V6: unbekanntes Feld semantik.{$f}";
            }
        }
        foreach ($def['fachdaten'] as $f => $c) { // V2/V3
            $v = $fach[$f] ?? null;
            if ($v === null) {
                if ($c['req']) {
                    $e[] = "V2: Pflichtfeld fachdaten.{$f} fehlt";
                }

                continue;
            }
            if ($c['typ'] === 'num') {
                if (! is_numeric($v)) {
                    $e[] = "V3: fachdaten.{$f} nicht numerisch";
                } elseif ($v < $c['min'] || $v > $c['max']) {
                    $e[] = "V2: fachdaten.{$f}={$v} außerhalb [{$c['min']},{$c['max']}]";
                }
            } elseif ($c['typ'] === 'bool' && ! is_bool($v)) {
                $e[] = "V3: fachdaten.{$f} nicht bool";
            } elseif ($c['typ'] === 'str' && ! is_string($v)) {
                $e[] = "V3: fachdaten.{$f} nicht string";
            }
        }
        if (isset($def['pflicht_alternativ'])) {
            $ok = false;
            foreach ($def['pflicht_alternativ'] as $gruppe) {
                $vollst = true;
                foreach ($gruppe as $f) {
                    if (($fach[$f] ?? $sem[$f] ?? null) === null) {
                        $vollst = false;
                        break;
                    }
                }
                if ($vollst) {
                    $ok = true;
                    break;
                }
            }
            if (! $ok) {
                $e[] = 'V: keine vollständige Pflicht-Gruppe (weder W35-Stützpunkte noch leistungskurve)';
            }
        }
        foreach ($def['paare'] ?? [] as [$a, $b]) { // V1
            if ((($fach[$a] ?? null) === null) !== (($fach[$b] ?? null) === null)) {
                $e[] = "V1: Betriebspunkt unvollständig — {$a}/{$b} nur paarweise (kW+COP derselben Betriebsart)";
            }
        }
        foreach ($def['semantik_pflicht'] ?? [] as $f) { // V4
            if (! empty($fach) && (($sem[$f] ?? null) === null || $sem[$f] === '')) {
                $e[] = "V4: semantik.{$f} Pflicht bei Spaltendaten";
            }
        }
        foreach ($def['herkunft'] as $f) { // V7
            if (($herk[$f] ?? null) === null || $herk[$f] === '') {
                $e[] = "V7: herkunft.{$f} fehlt";
            }
        }
        $vs = $herk['verifikations_status'] ?? null;
        if ($vs !== null && ! in_array($vs, ['datenblatt_verifiziert', 'importiert_ungeprueft'], true)) {
            $e[] = "V7: verifikations_status ungültig ({$vs})";
        }

        return $e;
    }

    /** Dry-Run: angelegt / geskippt (Dedup) / abgelehnt (Fehler). Kein Write. */
    public function dryRun(string $typ, array $geraete): array
    {
        $report = ['angelegt' => [], 'geskippt' => [], 'abgelehnt' => []];
        foreach ($geraete as $i => $g) {
            $id = $this->idOf($g, $i);
            $fehler = $this->validate($typ, $g);
            if ($fehler !== []) {
                $report['abgelehnt'][] = ['id' => $id, 'fehler' => $fehler];
            } elseif ($this->findExisting($g) !== null) {
                $report['geskippt'][] = $id;
            } else {
                $report['angelegt'][] = $id;
            }
        }

        return $report;
    }

    /**
     * --commit: schreibt valide Geräte in einer Transaction mit voller Herkunftskette.
     * Atomar bei Validierungsfehlern (Ablehnungen => nichts geschrieben). skip-if-exists Default;
     * --update mit Felddiff + Downgrade-Schutz (verifiziert bleibt ohne allow_downgrade unberührt).
     */
    public function commit(string $typ, array $geraete, array $opts = []): array
    {
        $importedFrom = $opts['imported_from'] ?? null;
        $update = (bool) ($opts['update'] ?? false);
        $allowDowngrade = (bool) ($opts['allow_downgrade'] ?? false);

        $valide = [];
        $abgelehnt = [];
        foreach ($geraete as $i => $g) {
            $id = $this->idOf($g, $i);
            $fehler = $this->validate($typ, $g);
            $fehler === [] ? $valide[] = [$g, $id] : $abgelehnt[] = ['id' => $id, 'fehler' => $fehler];
        }
        if ($abgelehnt !== []) {
            return ['batchId' => null, 'abbruch' => 'Validierungsfehler — nichts geschrieben (atomar)',
                'abgelehnt' => $abgelehnt, 'angelegt' => [], 'aktualisiert' => [], 'geskippt' => [], 'downgradeAbbruch' => [], 'diffs' => []];
        }

        $batchId = (string) Str::uuid();
        $now = now();
        $angelegt = $aktualisiert = $geskippt = $downgradeAbbruch = [];
        $diffs = [];

        DB::transaction(function () use ($typ, $valide, $batchId, $importedFrom, $update, $allowDowngrade, $now, &$angelegt, &$aktualisiert, &$geskippt, &$downgradeAbbruch, &$diffs) {
            foreach ($valide as [$g, $id]) {
                $existing = $this->findExisting($g);
                if ($existing === null) {
                    $this->insertGeraet($typ, $g, $batchId, $importedFrom, $now);
                    $angelegt[] = $id;
                } elseif (! $update) {
                    $geskippt[] = $id;
                } elseif ($existing->verifikations_status === 'datenblatt_verifiziert' && ! $allowDowngrade) {
                    $downgradeAbbruch[] = $id; // Downgrade-Schutz: verifizierten Bestand nicht still überschreiben
                } else {
                    $diffs[$id] = $this->updateGeraet($typ, $g, $existing, $batchId, $importedFrom, $allowDowngrade, $now);
                    $aktualisiert[] = $id;
                }
            }
            if ($angelegt !== [] || $aktualisiert !== []) {
                DB::table('spec_import_batches')->insert([
                    'id' => $batchId, 'geraetetyp' => $typ,
                    'modus' => $aktualisiert !== [] ? 'update' : 'insert',
                    'anzahl_angelegt' => count($angelegt), 'anzahl_aktualisiert' => count($aktualisiert),
                    'quelle' => $importedFrom, 'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        });

        return compact('batchId', 'angelegt', 'aktualisiert', 'geskippt', 'downgradeAbbruch', 'diffs') + ['abgelehnt' => []];
    }

    /**
     * Batch-Rückbau: entfernt AUSSCHLIESSLICH die Zeilen eines Batches über alle Tabellen.
     * Lehnt Update-Batches ab (überschriebene Vorwerte sind nicht wiederherstellbar — keine Schatten-Historie).
     */
    public function rollback(string $batchId): array
    {
        $batch = DB::table('spec_import_batches')->where('id', $batchId)->first();
        if ($batch === null) {
            return ['status' => 'unbekannt', 'meldung' => "Batch {$batchId} nicht gefunden"];
        }
        if ($batch->anzahl_aktualisiert > 0) {
            return ['status' => 'abgelehnt', 'meldung' => 'Update-Batch: überschriebene Vorwerte sind nicht wiederherstellbar — kein Rollback (manuell prüfen).'];
        }

        $geloescht = [];
        DB::transaction(function () use ($batchId, &$geloescht) {
            foreach (self::ZIEL_TABELLEN as $t) {
                $geloescht[$t] = DB::table($t)->where('import_batch_id', $batchId)->delete();
            }
            $geloescht['products'] = DB::table('products')->where('import_batch_id', $batchId)->delete();
            DB::table('spec_import_batches')->where('id', $batchId)->delete();
        });

        return ['status' => 'ok', 'geloescht' => $geloescht];
    }

    // ---- interne Schreib-Helfer ----

    private function insertGeraet(string $typ, array $g, string $batchId, ?string $importedFrom, $now): void
    {
        $ziel = SpecSchema::ziel($typ);
        $brandId = $this->brandId($g['identitaet']['hersteller'], $now);
        $agId = $this->groupId($ziel['category'], $now);

        $prod = $this->productsRow($typ, $g, $brandId, $agId, $batchId, $importedFrom, $now) + ['created_at' => $now];
        $pid = DB::table('products')->insertGetId($prod);

        DB::table($ziel['spec_tabelle'])->insert($this->specRow($typ, $g, $pid, $agId, $batchId, $now) + ['created_at' => $now]);
    }

    private function updateGeraet(string $typ, array $g, object $existing, string $batchId, ?string $importedFrom, bool $allowDowngrade, $now): array
    {
        $ziel = SpecSchema::ziel($typ);
        $brandId = $existing->brand_id;
        $agId = (int) $existing->article_group;

        $prod = $this->productsRow($typ, $g, $brandId, $agId, $batchId, $importedFrom, $now);
        if ($allowDowngrade && $existing->verifikations_status === 'datenblatt_verifiziert') {
            $prod['verifikations_status'] = 'importiert_ungeprueft'; // expliziter Downgrade
            Log::warning('spec:import Downgrade', ['product_id' => $existing->id, 'model' => $existing->model, 'batch' => $batchId, 'von' => 'datenblatt_verifiziert']);
        }
        $altSpec = (array) DB::table($ziel['spec_tabelle'])->where('product_id', $existing->id)->first();
        $neuSpec = $this->specRow($typ, $g, $existing->id, $agId, $batchId, $now);

        $diff = $this->felddiff((array) $existing, $prod, $altSpec, $neuSpec);

        DB::table('products')->where('id', $existing->id)->update($prod);
        DB::table($ziel['spec_tabelle'])->where('product_id', $existing->id)->update($neuSpec);

        return $diff;
    }

    private function productsRow(string $typ, array $g, int $brandId, int $agId, string $batchId, ?string $importedFrom, $now): array
    {
        $ziel = SpecSchema::ziel($typ);
        $row = [
            'brand_id' => $brandId, 'article_group' => (string) $agId,
            'product' => trim(($g['identitaet']['hersteller'] ?? '').' '.($g['identitaet']['modell'] ?? '')),
            'model' => $g['identitaet']['modell'], 'category' => $ziel['category'],
            'short_description' => $g['identitaet']['serie'] ?? null,
            'imported_from' => $importedFrom, 'import_batch_id' => $batchId,
            'verifikations_status' => $g['herkunft']['verifikations_status'] ?? null,
            'verifikations_datum' => $g['herkunft']['verifikations_datum'] ?? null,
            'datenblatt_referenz' => $g['herkunft']['datenblatt_referenz'] ?? null,
            'updated_at' => $now,
        ];
        foreach ($ziel['products'] as $col => $pfad) {
            $row[$col] = $this->pfad($g, $pfad);
        }

        return $row;
    }

    private function specRow(string $typ, array $g, int $pid, int $agId, string $batchId, $now): array
    {
        $ziel = SpecSchema::ziel($typ);
        $cols = Schema::getColumnListing($ziel['spec_tabelle']);
        $row = ['product_id' => $pid, 'import_batch_id' => $batchId, 'updated_at' => $now];

        foreach (($g['fachdaten'] ?? []) as $f => $v) {
            if (in_array($f, $cols, true)) {
                $row[$f] = $v;
            }
        }
        foreach (($g['semantik'] ?? []) as $f => $v) {
            if (in_array($f, $cols, true)) {
                $row[$f] = $v;
            }
        }
        foreach ($ziel['spec_extra'] as $col => $pfad) {
            if (in_array($col, $cols, true)) {
                $row[$col] = $this->pfad($g, $pfad);
            }
        }
        if (in_array('company', $cols, true)) {
            $row['company'] = $g['identitaet']['hersteller'];
        }
        if (in_array('name', $cols, true)) {
            $row['name'] = $g['identitaet']['modell'];
        }
        if (in_array('article_group_id', $cols, true)) {
            $row['article_group_id'] = $agId;
        }

        return $row;
    }

    /** Feld-Diff über products + Spec-Zeile: [feld => [alt, neu]] für tatsächliche Änderungen. */
    private function felddiff(array $altProd, array $neuProd, array $altSpec, array $neuSpec): array
    {
        $diff = [];
        foreach ($neuProd as $k => $v) {
            if ($k === 'updated_at' || $k === 'import_batch_id') {
                continue;
            }
            if (($altProd[$k] ?? null) != $v) {
                $diff["products.{$k}"] = [$altProd[$k] ?? null, $v];
            }
        }
        foreach ($neuSpec as $k => $v) {
            if ($k === 'updated_at' || $k === 'import_batch_id' || $k === 'product_id') {
                continue;
            }
            if (($altSpec[$k] ?? null) != $v) {
                $diff["spec.{$k}"] = [$altSpec[$k] ?? null, $v];
            }
        }

        return $diff;
    }

    private function findExisting(array $g): ?object
    {
        $hersteller = $g['identitaet']['hersteller'] ?? null;
        $modell = $g['identitaet']['modell'] ?? null;
        if ($hersteller === null || $modell === null) {
            return null;
        }
        $brandId = DB::table('brands')->where('name', $hersteller)->value('id');
        if ($brandId === null) {
            return null;
        }

        return DB::table('products')->where('brand_id', $brandId)->where('model', $modell)->first();
    }

    private function brandId(string $name, $now): int
    {
        $status = DB::table('brands')->whereNotNull('status')->value('status') ?? 'active';

        return DB::table('brands')->where('name', $name)->value('id')
            ?? DB::table('brands')->insertGetId(['name' => $name, 'status' => $status, 'created_at' => $now, 'updated_at' => $now]);
    }

    private function groupId(string $name, $now): int
    {
        return DB::table('article_groups')->where('article_group', $name)->value('id')
            ?? DB::table('article_groups')->insertGetId(['article_group' => $name, 'created_at' => $now, 'updated_at' => $now]);
    }

    private function pfad(array $g, string $pfad)
    {
        [$block, $feld] = explode('.', $pfad, 2);

        return $g[$block][$feld] ?? null;
    }

    private function idOf(array $g, int $i): string
    {
        return trim(($g['identitaet']['hersteller'] ?? '?').' '.($g['identitaet']['modell'] ?? "#{$i}"));
    }

    /** CSV mit flachem Header → kanonische Blöcke via SpecSchema. */
    private function csvZuKanonisch(string $inhalt): array
    {
        $zeilen = array_map('str_getcsv', array_filter(explode("\n", trim($inhalt)), fn ($z) => trim($z) !== ''));
        $header = array_map('trim', array_shift($zeilen));
        $out = [];
        foreach ($zeilen as $row) {
            $flat = array_combine($header, array_map(fn ($v) => trim((string) $v) === '' ? null : $v, $row));
            $typ = $flat['geraetetyp'] ?? $flat['kategorie'] ?? null;
            $def = SpecSchema::isType((string) $typ) ? SpecSchema::definition((string) $typ) : null;
            $g = ['geraetetyp' => $typ, 'identitaet' => [], 'fachdaten' => [], 'semantik' => [], 'herkunft' => []];
            foreach ($flat as $k => $v) {
                if ($k === 'geraetetyp') {
                    continue;
                }
                if (in_array($k, ['hersteller', 'modell', 'serie', 'kategorie'], true)) {
                    $g['identitaet'][$k] = $v;
                } elseif (in_array($k, ['verifikations_status', 'verifikations_datum', 'datenblatt_referenz', 'quelle_url', 'imported_from', 'datenquelle'], true)) {
                    $g['herkunft'][$k] = $v;
                } elseif (in_array($k, ['kurve_semantik', 'leistungskurve'], true)) {
                    $g['semantik'][$k] = $v;
                } elseif ($def && isset($def['fachdaten'][$k]) && $v !== null) {
                    $g['fachdaten'][$k] = $def['fachdaten'][$k]['typ'] === 'num' ? (float) $v : $v;
                } elseif ($v !== null) {
                    $g['fachdaten'][$k] = $v;
                }
            }
            $out[] = $g;
        }

        return $out;
    }
}
