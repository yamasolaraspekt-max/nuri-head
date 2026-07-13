<?php

namespace App\Services\Offer;

use App\Models\LeadAlternativeAdd;
use App\Models\LeadProductList;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * P3-d0a (Bereich 2) — READ-ONLY WP-Katalog-Matching-Diagnose.
 *
 * Stellt technische Geräte-Kandidaten (`product_heat_pump_specs`) den Preis-Set-Produkten
 * (`master_set_components` des WP-Sets) gegenüber und macht sichtbar, WARUM kein automatischer
 * Preisanker gesetzt werden darf (Schnittmenge über `product_id`).
 *
 * Verbindliche Regeln (Yama-Freigabe P3-d0a, 2026-07-13):
 *  - READ-ONLY: kein Write, keine Persistenz, keine Katalogänderung.
 *  - KEIN Preisanker, KEIN `component_id` für Übernahme, KEINE Preisberechnung, KEINE Angebotsposition.
 *  - VK/EK der Set-Komponenten NUR diagnostisch anzeigen („Preisprodukt vorhanden, ohne Kennlinie").
 *  - Nur WP (`article_groups.id=2`); `id=16` (=„Tapete") wird NIE verwendet.
 *  - Auto-`component_id` bleibt NEIN — dieser Service SETZT keinen Anker, er DIAGNOSTIZIERT nur.
 */
class WpKatalogMatchingService
{
    private const GEWERK_WP = 2;

    /** @return array<string,mixed> */
    public function fuerId(int $leadProductListId): array
    {
        $lpl = LeadProductList::with('alternative')->find($leadProductListId);

        if (! $lpl) {
            return $this->nichtAnwendbar('Vorgang nicht gefunden.');
        }
        if ((int) $lpl->product_id !== self::GEWERK_WP) {
            return $this->nichtAnwendbar('Nur für Wärmepumpe (Gewerk WP) anwendbar.');
        }

        try {
            return $this->diagnose($lpl);
        } catch (Throwable $e) {
            return $this->nichtAnwendbar('Katalog-Diagnose nicht lesbar.');
        }
    }

    /** @return array<string,mixed> */
    private function diagnose(LeadProductList $lpl): array
    {
        $kandidatenRoh = $this->kandidatenLesen();     // technische Wahrheit (Specs)
        $setRoh = $this->setProdukteLesen();           // Preis-Wahrheit (Set)

        $setPidMap = [];
        foreach ($setRoh as $s) {
            if ($s->product_id !== null) {
                $setPidMap[(int) $s->product_id][] = $s;
            }
        }
        $specPids = [];
        foreach ($kandidatenRoh as $k) {
            if ($k->product_id !== null) {
                $specPids[(int) $k->product_id] = $k;
            }
        }

        // Kandidaten-Sicht (links)
        $kandidaten = [];
        foreach ($kandidatenRoh as $k) {
            $pid = $k->product_id !== null ? (int) $k->product_id : null;
            $bepreist = $this->bepreist($k->retail_price ?? null, $k->purchase_price ?? null);
            $imSet = $pid !== null && isset($setPidMap[$pid]);
            $konfliktFelder = $imSet ? $this->artikelgruppenKonflikt($k->article_group ?? null) : [];

            if ($imSet && $konfliktFelder) {
                $status = 'konflikt';
            } elseif ($imSet) {
                $status = count($setPidMap[$pid]) > 1 ? 'mehrere_treffer' : 'eindeutig_gemappt';
            } else {
                $status = $bepreist ? 'kein_treffer' : 'kandidat_unbepreist';
            }

            $kandidaten[] = [
                'hersteller' => $k->hersteller ?: '—',
                'modell' => $k->modell ?: ($k->produkt ?: '—'),
                'leistung_kw' => $k->leistung_kw !== null ? (float) $k->leistung_kw : null,
                'scop' => $k->scop !== null ? (float) $k->scop : null,
                'bepreist' => $bepreist,
                'im_set' => $imSet,
                'status' => $status,
                'konflikt_felder' => $konfliktFelder,
            ];
        }

        // Set-Sicht (rechts) — VK/EK NUR diagnostisch
        $setProdukte = [];
        foreach ($setRoh as $s) {
            $pid = $s->product_id !== null ? (int) $s->product_id : null;
            $hatSpecs = $pid !== null && isset($specPids[$pid]);
            $konfliktFelder = $hatSpecs ? $this->artikelgruppenKonflikt($s->article_group ?? null) : [];

            if ($hatSpecs && $konfliktFelder) {
                $status = 'konflikt';
            } elseif ($hatSpecs) {
                $status = 'eindeutig_gemappt';
            } else {
                $status = 'set_ohne_specs';
            }

            $setProdukte[] = [
                'name' => $s->produkt ?: ($s->modell ?: '—'),
                'hersteller' => $s->hersteller ?: '—',
                'article_no' => $s->article_no ?: '—',
                'vk_diagnostisch' => $s->unit_price !== null ? (float) $s->unit_price : null,
                'ek_diagnostisch' => $s->purchase_price !== null ? (float) $s->purchase_price : null,
                'hat_specs' => $hatSpecs,
                'status' => $status,
                'konflikt_felder' => $konfliktFelder,
            ];
        }

        // Schnittmenge (gemeinsames product_id)
        $schnittmenge = count(array_intersect(array_keys($specPids), array_keys($setPidMap)));
        $autoAnkerMoeglich = false; // in P3-d0a IMMER false — dieser Service setzt nie einen Anker.

        return [
            'anwendbar' => true,
            'gewerk_id' => self::GEWERK_WP,
            'bedarf_kw' => $this->bedarfKw($lpl),
            'schnittmenge' => $schnittmenge,
            'auto_anker_moeglich' => $autoAnkerMoeglich,
            'banner' => $schnittmenge === 0
                ? 'Kein automatischer Preisanker möglich — Schnittmenge (gemeinsames product_id) = 0. Manuelle Zuordnung oder Katalog-Kuratierung erforderlich.'
                : 'Technische Kandidaten und Preis-Set-Produkte überschneiden sich (product_id) — Auto-Anker bleibt dennoch NEIN (nur Diagnose, Bestätigung/Kuratierung nötig).',
            'naechste_aufgabe' => 'Manuelle Zuordnung Gerät ↔ Preis-Komponente ODER Katalog-Kuratierung: Preis UND Kennlinie auf demselben product_id konsolidieren.',
            'kandidaten' => $kandidaten,
            'set_produkte' => $setProdukte,
            'hinweis' => 'Read-only Diagnose. Kein Preisanker, kein component_id, keine Angebotsposition. VK/EK nur diagnostisch.',
        ];
    }

    // ---- Lesen (read-only) ----

    /** @return array<int,object> */
    private function kandidatenLesen(): array
    {
        return DB::table('product_heat_pump_specs as s')
            ->leftJoin('products as p', 'p.id', '=', 's.product_id')
            ->leftJoin('brands as b', 'b.id', '=', 'p.brand_id')
            ->select([
                's.product_id',
                'p.product as produkt',
                'p.model as modell',
                'b.name as hersteller',
                's.heizleistung_a7_w35_kw as leistung_kw',
                's.scop_35 as scop',
                'p.retail_price',
                'p.purchase_price',
                'p.article_group',
            ])
            ->orderBy('s.product_id')
            ->get()
            ->all();
    }

    /** @return array<int,object> */
    private function setProdukteLesen(): array
    {
        $setId = DB::table('master_sets')->where('article_group_id', self::GEWERK_WP)->orderBy('id')->value('id');
        if ($setId === null) {
            return [];
        }

        return DB::table('master_set_components as c')
            ->leftJoin('products as p', 'p.id', '=', 'c.product_id')
            ->leftJoin('brands as b', 'b.id', '=', 'p.brand_id')
            ->where('c.master_set_id', $setId)
            ->select([
                'c.product_id',
                'c.article_no',
                'c.unit_price',
                'c.purchase_price',
                'p.product as produkt',
                'p.model as modell',
                'b.name as hersteller',
                'p.article_group',
            ])
            ->orderBy('c.id')
            ->get()
            ->all();
    }

    // ---- Helfer ----

    private function bepreist($vk, $ek): bool
    {
        return ($vk !== null && (float) $vk > 0) || ($ek !== null && (float) $ek > 0);
    }

    /**
     * Echter, erkennbarer Konflikt bei gemapptem product_id: das Produkt ist nicht als WP
     * klassifiziert (article_group ≠ 2). Ein gemeinsames product_id = EIN Produkt — Hersteller/Modell
     * können dann nicht divergieren, wohl aber die Gewerk-Zuordnung (Katalog-Datenfehler).
     * @return array<int,string>
     */
    private function artikelgruppenKonflikt($articleGroup): array
    {
        if ($articleGroup !== null && (int) $articleGroup !== self::GEWERK_WP) {
            return ["Produkt nicht als WP klassifiziert (article_group={$articleGroup})"];
        }

        return [];
    }

    private function bedarfKw(LeadProductList $lpl): ?float
    {
        try {
            $profil = \App\Models\Anforderungsprofil::query()->aktiv()
                ->where(function ($w) use ($lpl) {
                    $w->where(function ($a) use ($lpl) {
                        $a->where('verankerbar_type', LeadAlternativeAdd::class)->where('verankerbar_id', $lpl->alternative_id);
                    })->orWhere(function ($a) use ($lpl) {
                        $a->where('verankerbar_type', LeadProductList::class)->where('verankerbar_id', $lpl->id);
                    });
                })->with('werte')->orderByDesc('version')->first();

            if (! $profil) {
                return null;
            }
            foreach ($profil->werte as $w) {
                if (in_array($w->schluessel, ['phi_hl_kw', 'standardheizlast_kw'], true) && $w->wert_num !== null) {
                    return (float) $w->wert_num;
                }
            }

            return null;
        } catch (Throwable $e) {
            return null;
        }
    }

    /** @return array<string,mixed> */
    private function nichtAnwendbar(string $grund): array
    {
        return [
            'anwendbar' => false,
            'gewerk_id' => 0,
            'bedarf_kw' => null,
            'schnittmenge' => 0,
            'auto_anker_moeglich' => false,
            'banner' => $grund,
            'naechste_aufgabe' => '',
            'kandidaten' => [],
            'set_produkte' => [],
            'hinweis' => $grund,
        ];
    }
}
