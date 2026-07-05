<?php

namespace App\Services\Spec;

use Illuminate\Support\Facades\DB;

/**
 * Geräte-Spec-Import: Parsing (JSON kanonisch / CSV Zubringer) + Validierung gegen SpecSchema (V1–V7)
 * + Dry-Run-Report. Baustufe 1: DB-frei bis auf den read-only Dedup-Check; KEIN Write (--commit = Baustufe 2).
 *
 * docs/spec-import/00-spec-standard.md §3 (Regeln), §4 (Ablauf).
 */
class SpecImportService
{
    /**
     * Liest JSON (ein Objekt, Array von Objekten, oder {geraete:[...]}) oder CSV (flacher Header) in
     * kanonische Geräte-Objekte. Wirft bei kaputtem JSON.
     *
     * @return list<array<string,mixed>>
     */
    public function parse(string $pfad): array
    {
        $ext = strtolower(pathinfo($pfad, PATHINFO_EXTENSION));
        $inhalt = (string) file_get_contents($pfad);

        if ($ext === 'csv') {
            return $this->csvZuKanonisch($inhalt);
        }

        $data = json_decode($inhalt, true, 512, JSON_THROW_ON_ERROR);
        if (isset($data['geraetetyp'])) {
            return [$data]; // einzelnes Objekt
        }

        return array_values($data['geraete'] ?? $data); // {geraete:[…]} oder direktes Array
    }

    /**
     * Validiert ein Gerät gegen die Regelquelle. Leere Liste = valide.
     *
     * @return list<string> Fehlerliste (V-Regel + Feld)
     */
    public function validate(string $typ, array $g): array
    {
        $def = SpecSchema::definition($typ);
        $fach = $g['fachdaten'] ?? [];
        $sem = $g['semantik'] ?? [];
        $herk = $g['herkunft'] ?? [];
        $e = [];

        // V5 — Identität vollständig
        foreach ($def['identitaet'] as $f) {
            if (($g['identitaet'][$f] ?? null) === null || $g['identitaet'][$f] === '') {
                $e[] = "V5: identitaet.{$f} fehlt";
            }
        }

        // V6 — unbekannte Felder (fachdaten + semantik) => Ablehnung, nie stilles Verwerfen
        foreach (array_keys($fach) as $f) {
            if (! isset($def['fachdaten'][$f])) {
                $e[] = "V6: unbekanntes Feld fachdaten.{$f}";
            }
        }
        foreach (array_keys($sem) as $f) {
            if (! in_array($f, $def['semantik_erlaubt'] ?? [], true)) {
                $e[] = "V6: unbekanntes Feld semantik.{$f}";
            }
        }

        // V2/V3 — Typ, Einheiten-Plausibilität, Pflichtfelder
        foreach ($def['fachdaten'] as $f => $c) {
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

        // Pflicht-alternativ — eine vollständige Stützpunkt-Gruppe (W35-Punkte ODER dichte leistungskurve)
        if (isset($def['pflicht_alternativ'])) {
            $ok = false;
            foreach ($def['pflicht_alternativ'] as $gruppe) {
                $vollstaendig = true;
                foreach ($gruppe as $f) {
                    if (($fach[$f] ?? $sem[$f] ?? null) === null) {
                        $vollstaendig = false;
                        break;
                    }
                }
                if ($vollstaendig) {
                    $ok = true;
                    break;
                }
            }
            if (! $ok) {
                $e[] = 'V: keine vollständige Pflicht-Gruppe (weder W35-Stützpunkte noch leistungskurve)';
            }
        }

        // V1 — kW + COP eines Punkts nur paarweise (keine Misch-/Halbpunkte, Buderus-Lektion)
        foreach ($def['paare'] ?? [] as [$a, $b]) {
            if ((($fach[$a] ?? null) === null) !== (($fach[$b] ?? null) === null)) {
                $e[] = "V1: Betriebspunkt unvollständig — {$a}/{$b} nur paarweise (kW+COP derselben Betriebsart)";
            }
        }

        // V4 — kurve_semantik Pflicht, sobald Spalten-Fachdaten vorliegen
        foreach ($def['semantik_pflicht'] ?? [] as $f) {
            if (! empty($fach) && (($sem[$f] ?? null) === null || $sem[$f] === '')) {
                $e[] = "V4: semantik.{$f} Pflicht bei Spaltendaten";
            }
        }

        // V7 — Herkunft / Prüfstand
        foreach ($def['herkunft'] as $f) {
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

    /**
     * Dry-Run: klassifiziert jedes Gerät in angelegt / geskippt (Dedup) / abgelehnt (mit Fehlern). Kein Write.
     */
    public function dryRun(string $typ, array $geraete): array
    {
        $report = ['angelegt' => [], 'geskippt' => [], 'abgelehnt' => []];
        foreach ($geraete as $i => $g) {
            $id = trim(($g['identitaet']['hersteller'] ?? '?').' '.($g['identitaet']['modell'] ?? "#{$i}"));
            $fehler = $this->validate($typ, $g);
            if ($fehler !== []) {
                $report['abgelehnt'][] = ['id' => $id, 'fehler' => $fehler];
            } elseif ($this->existiert($g)) {
                $report['geskippt'][] = $id;
            } else {
                $report['angelegt'][] = $id;
            }
        }

        return $report;
    }

    /** Read-only Dedup-Check auf (brand_id, model) — via brand-Name, ohne anzulegen. */
    private function existiert(array $g): bool
    {
        $hersteller = $g['identitaet']['hersteller'] ?? null;
        $modell = $g['identitaet']['modell'] ?? null;
        if ($hersteller === null || $modell === null) {
            return false;
        }
        $brandId = DB::table('brands')->where('name', $hersteller)->value('id');
        if ($brandId === null) {
            return false;
        }

        return DB::table('products')->where('brand_id', $brandId)->where('model', $modell)->exists();
    }

    /** CSV mit flachem Header → kanonische Blöcke (identitaet/fachdaten/semantik/herkunft) via SpecSchema. */
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
                    $g['fachdaten'][$k] = $v; // unbekannt -> bewusst behalten, V6 lehnt ab
                }
            }
            $out[] = $g;
        }

        return $out;
    }
}
