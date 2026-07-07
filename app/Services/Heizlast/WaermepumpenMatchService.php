<?php

namespace App\Services\Heizlast;

use App\Repositories\CatalogDeviceRepository;
use App\Services\Energie\Contracts\WpKennlinie;

/**
 * Matcht die berechnete WP-Heizleistung gegen die Geräte-Datenbank und liefert
 * passende Wärmepumpen (Hersteller/Typ/kW) mit COP/SCOP, Deckungsgrad und Ampel.
 * Auslegungspunkt: A-7 (Norm-Außentemperatur) bei W35 (FBH) bzw. W55 (Heizkörper).
 *
 * Adapter-Naht (ticket): Geräte kommen aus CatalogDeviceRepository->heatPumps() (ticket-Katalog,
 * eine Wahrheit) statt aus dem wberechnung-Eloquent-Model. Match-/Bewertungslogik unverändert (Diff=0).
 */
class WaermepumpenMatchService
{
    public function __construct(private CatalogDeviceRepository $repo = new CatalogDeviceRepository) {}

    /**
     * @return array{geraete: array<int, array<string, mixed>>, design_punkt: ?string, hinweis: ?string}
     */
    public function kandidaten(float $benoetigtKw, string $wpTyp, string $heizsystem, float $vorlaufC, int $limit = 6): array
    {
        // Die Geräte-DB enthält aktuell ausschließlich Luft/Wasser-Monoblocks (R290).
        if ($wpTyp !== 'luft_wasser') {
            return ['geraete' => [], 'design_punkt' => null, 'hinweis' => 'Die Geräte-Datenbank enthält aktuell nur Luft/Wasser-Wärmepumpen.'];
        }
        if ($benoetigtKw <= 0) {
            return ['geraete' => [], 'design_punkt' => null, 'hinweis' => null];
        }

        $useW55 = in_array($heizsystem, ['heizkoerper', 'beides'], true);

        $geraete = $this->repo->heatPumps()->map(function (WpKennlinie $w) use ($benoetigtKw, $useW55, $vorlaufC) {
            $w55Fehlt = $w->heizleistung_am7_w55_kw === null;
            $leistung = $useW55 ? ($w->heizleistung_am7_w55_kw ?? $w->heizleistung_am7_w35_kw) : $w->heizleistung_am7_w35_kw;
            if (! $leistung) {
                return null;
            }
            $cop = $useW55 ? ($w->cop_am7_w55 ?? $w->cop_am7_w35) : $w->cop_am7_w35;
            $scop = $useW55 ? ($w->scop_55 ?? $w->scop_35) : $w->scop_35;
            $deckung = $leistung / $benoetigtKw;

            [$status, $hinweis] = $this->bewerten($w, $leistung, $benoetigtKw, $deckung, $vorlaufC, $useW55 && $w55Fehlt);

            return [
                'id' => $w->id,
                'hersteller' => $w->hersteller,
                'serie' => $w->serie,
                'modell' => $w->modell,
                'geraetetyp' => $w->geraetetyp,
                'kaeltemittel' => $w->kaeltemittel,
                'leistung_kw' => round($leistung, 2),
                'cop' => $cop,
                'scop' => $scop,
                'max_vorlauf_c' => $w->max_vorlauf_c,
                'modulation_min_kw' => $w->modulation_min_kw,
                'modulation_max_kw' => $w->modulation_max_kw,
                'deckung_pct' => round($deckung * 100),
                'status' => $status,
                'hinweis' => $hinweis,
            ];
        })->filter()->values();

        $rang = ['passt' => 0, 'monoenergetisch' => 1, 'zu_gross' => 2, 'zu_klein' => 3];
        $geraete = $geraete
            ->sortBy(fn ($g) => [$rang[$g['status']] ?? 9, abs($g['deckung_pct'] - 105)])
            ->take($limit)->values();

        return [
            'geraete' => $geraete->all(),
            'design_punkt' => $useW55 ? 'A-7 / W55' : 'A-7 / W35',
            'hinweis' => null,
        ];
    }

    /**
     * @return array{0: string, 1: ?string}
     */
    private function bewerten(WpKennlinie $w, float $leistung, float $benoetigt, float $deckung, float $vorlaufC, bool $w55Geschaetzt): array
    {
        if ($w->max_vorlauf_c !== null && $vorlaufC > $w->max_vorlauf_c) {
            return ['zu_klein', "Max. Vorlauf {$w->max_vorlauf_c} °C < benötigt ".round($vorlaufC).' °C'];
        }

        $hinweise = [];
        if ($w55Geschaetzt) {
            $hinweise[] = 'W55 nicht spezifiziert – W35-Wert verwendet';
        }
        if ($w->modulation_min_kw !== null && $w->modulation_min_kw > $benoetigt) {
            $hinweise[] = 'Mindestmodulation '.$w->modulation_min_kw.' kW > Bedarf – Taktgefahr im Teillastbetrieb';
        }

        if ($deckung < 0.85) {
            $status = 'zu_klein';
            $hinweise[] = 'deckt die Heizlast bei A-7 nicht (nur '.round($deckung * 100).' %)';
        } elseif ($deckung < 1.0) {
            $status = 'monoenergetisch';
            $hinweise[] = 'leicht unterdimensioniert – monoenergetisch (Heizstab) für die Spitze sinnvoll';
        } elseif ($deckung <= 1.3) {
            $status = 'passt';
        } else {
            $status = 'zu_gross';
            $hinweise[] = 'überdimensioniert ('.round($deckung * 100).' %) – Taktgefahr, höhere Kosten';
        }

        return [$status, $hinweise ? implode('; ', $hinweise) : null];
    }
}
