<?php

namespace App\Http\Controllers\Heizkoerper;

use App\Http\Controllers\Controller;
use App\Models\DealMeasurement;
use App\Models\DealMeasurementItem;
use App\Models\RadiatorInstallation;
use App\Models\RadiatorSpec;
use App\Services\Heizkoerper\CompatibilityService;
use App\Services\Heizkoerper\RadiatorCatalogAdapter;
use App\Services\Heizkoerper\RadiatorPerformanceService;
use App\Services\Heizkoerper\RadiatorSituation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Rechen-/Kompatibilitäts-Endpunkte des Heizkörper-Moduls (M4-a v-a).
 * Reiner Durchstich über die abgenommenen Kerne (M3): RadiatorCatalogAdapter ->
 * RadiatorPerformanceService (EN-442) bzw. CompatibilityService (D3-Regeln). Keine eigene
 * Physik/Regel-Logik hier. Hinter Feature-Flag (EnsureHeizkoerperEnabled) + 'auth'.
 */
class HeizkoerperController extends Controller
{
    public function __construct(
        private RadiatorCatalogAdapter $adapter = new RadiatorCatalogAdapter,
        private RadiatorPerformanceService $performance = new RadiatorPerformanceService,
        private CompatibilityService $compatibility = new CompatibilityService,
    ) {}

    /** GET heizkoerper.konfigurator → Alpine-Fläche (Live-EN-442 + Ampel + Kompatibilität). */
    public function konfigurator()
    {
        // Optionale Auswahl bestehender Aufnahmen; Direkteingabe funktioniert auch ohne Daten.
        $installations = RadiatorInstallation::query()
            ->latest('id')->limit(50)
            ->get(['id', 'room', 'radiator_spec_id', 'width', 'anzahl']);

        return view('admin.heizkoerper.konfigurator', compact('installations'));
    }

    /** POST heizkoerper.berechnen → EN-442-Tabelle (75→35), Ampel, Mindest-Vorlauf. */
    public function berechnen(Request $request): JsonResponse
    {
        $data = $request->validate([
            'installation_id' => ['nullable', 'integer', 'exists:radiator_installations,id'],
            'radiator_spec_id' => ['nullable', 'integer', 'exists:product_radiator_specs,id'],
            'baulaenge_mm' => ['nullable', 'numeric', 'min:1'],
            'anzahl' => ['nullable', 'integer', 'min:1'],
            'vorlauf' => ['required', 'numeric', 'min:20', 'max:90'],
            'raumtemp' => ['nullable', 'numeric', 'min:0', 'max:30'],
            'spreizung' => ['required', 'numeric', 'min:1', 'max:30'],
            'heizlast_w' => ['nullable', 'numeric', 'min:0'],
        ]);

        [$spec, $baulaengeMm, $anzahl] = $this->resolveSpec($data);
        if (! $spec) {
            return response()->json(['message' => 'radiator_spec_id oder installation_id mit hinterlegtem Katalog-Spec erforderlich.'], 422);
        }

        $raumtemp = (float) ($data['raumtemp'] ?? 20);
        $spreizung = (float) $data['spreizung'];
        $vorlauf = (float) $data['vorlauf'];

        // Baulänge width ist mm (Ur-Table integer) -> Adapter erwartet Meter.
        $entry = $this->adapter->toQRealEntry($spec, $baulaengeMm / 1000, $anzahl);
        $hk = [$entry];

        $qReal = $this->performance->qReal($hk, $vorlauf, $raumtemp, $spreizung);
        $tabelle = $this->performance->leistungstabelle($hk, $raumtemp);

        $heizlast = isset($data['heizlast_w']) ? (float) $data['heizlast_w'] : null;
        $ampel = $heizlast !== null ? $this->performance->status($qReal, $heizlast) : 'na';
        $minVorlauf = $heizlast !== null ? $this->performance->minVorlauf($hk, $heizlast, $raumtemp, $spreizung) : null;

        return response()->json([
            'q_norm_w' => round($entry['q_norm_w'], 1),
            'q_real' => round($qReal, 1),
            'en442_tabelle' => $tabelle,
            'ampel' => $ampel,
            'min_vorlauf' => $minVorlauf,
        ]);
    }

    /** POST heizkoerper.kompatibilitaet → CompatibilityService (D3), inkl. datenqualitaet + Begründungen. */
    public function kompatibilitaet(Request $request): JsonResponse
    {
        $data = $request->validate([
            'installation_id' => ['nullable', 'integer', 'exists:radiator_installations,id'],
            'ist_ventil_heizkoerper' => ['nullable', 'boolean'],
            'anschluss_fuehrung' => ['nullable', 'in:zweirohr,einrohr'],
            'kopf_norm_bestand' => ['nullable', 'in:M30x1_5,RA,RAV,RAVL,sonstige'],
            'hk_hersteller' => ['nullable', 'string', 'max:191'],
            'hk_serie' => ['nullable', 'string', 'max:191'],
            'baujahr' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'heizlast_w' => ['nullable', 'numeric', 'min:0'],
            'spreizung' => ['nullable', 'numeric', 'min:1', 'max:30'],
            'dp_ventil_mbar' => ['nullable', 'numeric', 'min:1'],
        ]);

        $result = $this->compatibility->fuerHeizkoerper($this->resolveSituation($data));

        return response()->json([
            'datenqualitaet' => $result->datenqualitaet,
            'positionen' => $result->positionen,
            'voreinstellstufe' => $result->voreinstellstufe,
            'sperren' => $result->sperren,
            'hinweise' => $result->hinweise,
        ]);
    }

    /**
     * POST heizkoerper.stueckliste.uebernehmen → additive deal_measurement_items (kind='heizkoerper')
     * je Raum, Replace-per-Raum (Soft-Delete eigener HK-Zeilen dieses Raums + frischer Satz, 1 Transaction).
     * Positionen serverseitig re-derived (kein Client-Vertrauen). Guards = Bestandsmuster: Flag+auth (Route),
     * status='completed'→423. Ownership-Gap ist bekannt/eskaliert (Bestandslage), hier NICHT gefixt.
     * Preis-Spalten sind non-nullable default(0) → werden NICHT gesetzt; „kein Preis" lebt in raw_snapshot+note,
     * article_no nur bei serien-präzise.
     */
    public function uebernehmen(Request $request): JsonResponse
    {
        $data = $request->validate([
            'deal_measurement_id' => ['required', 'integer', 'exists:deal_measurements,id'],
            'section_title' => ['required', 'string', 'max:191'],
            'installation_id' => ['nullable', 'integer', 'exists:radiator_installations,id'],
            'radiator_spec_id' => ['nullable', 'integer', 'exists:product_radiator_specs,id'],
            'baulaenge_mm' => ['nullable', 'numeric', 'min:1'],
            'anzahl' => ['nullable', 'integer', 'min:1'],
            'vorlauf' => ['nullable', 'numeric', 'min:20', 'max:90'],
            'raumtemp' => ['nullable', 'numeric', 'min:0', 'max:30'],
            'spreizung' => ['nullable', 'numeric', 'min:1', 'max:30'],
            'ist_ventil_heizkoerper' => ['nullable', 'boolean'],
            'anschluss_fuehrung' => ['nullable', 'in:zweirohr,einrohr'],
            'kopf_norm_bestand' => ['nullable', 'in:M30x1_5,RA,RAV,RAVL,sonstige'],
            'hk_hersteller' => ['nullable', 'string', 'max:191'],
            'hk_serie' => ['nullable', 'string', 'max:191'],
            'baujahr' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'heizlast_w' => ['nullable', 'numeric', 'min:0'],
            'dp_ventil_mbar' => ['nullable', 'numeric', 'min:1'],
        ]);

        $measurement = DealMeasurement::findOrFail($data['deal_measurement_id']);
        if ($measurement->status === 'completed') {
            return response()->json(['message' => 'Dieses Aufmaß ist abgeschlossen und gesperrt. Bitte zuerst entsperren.'], 423);
        }

        // Positionen serverseitig aufbauen: HK-Position + Zubehör (aus CompatibilityService).
        [$spec, , $anzahl] = $this->resolveSpec($data);
        $result = $this->compatibility->fuerHeizkoerper($this->resolveSituation($data));

        $rows = [];
        if ($spec) {
            $rows[] = [
                'kategorie' => 'heizkoerper',
                'name' => trim('Heizkörper ' . ($spec->hersteller ?? '') . ' ' . ($spec->typ ?? '')),
                'herst_artikelnr' => null,
                'accessory_id' => null,
                'menge' => $anzahl,
                'begruendung' => 'Heizkörper-Position (Katalog-Spec ' . $spec->id . ')',
            ];
        }
        foreach ($result->positionen as $p) {
            $rows[] = [
                'kategorie' => $p['kategorie'],
                'name' => trim(($p['hersteller'] ?? '') . ' ' . ($p['name'] ?? '')),
                'herst_artikelnr' => $p['herst_artikelnr'] ?? null,
                'accessory_id' => $p['accessory_id'] ?? null,
                'menge' => 1,
                'begruendung' => $p['begruendung'] ?? '',
            ];
        }

        if ($rows === []) {
            return response()->json(['message' => 'Kein Positions-Satz zum Übernehmen — radiator_spec_id/installation_id oder kompatibles Zubehör erforderlich.'], 422);
        }

        $serienPraezise = $result->datenqualitaet === 'serien-praezise';
        $section = $data['section_title'];

        $created = DB::transaction(function () use ($measurement, $section, $rows, $serienPraezise, $result, $data) {
            // Replace-per-Raum: NUR eigene kind='heizkoerper'-Zeilen dieses Raums soft-löschen (Fremdzeilen unberührt).
            DealMeasurementItem::where('deal_measurement_id', $measurement->id)
                ->where('section_title', $section)
                ->where('kind', 'heizkoerper')
                ->delete();

            $sort = 0;
            $out = [];
            foreach ($rows as $row) {
                $out[] = DealMeasurementItem::create([
                    'deal_measurement_id' => $measurement->id,
                    'deal_id' => $measurement->deal_id,
                    'offer_id' => $measurement->offer_id,
                    'section_title' => $section,
                    'sort_order' => $sort++,
                    'item_type' => 'heizkoerper',
                    'kind' => 'heizkoerper',
                    'name' => $row['name'] !== '' ? $row['name'] : 'Heizkörper-Position',
                    // article_no nur bei serien-präzise; Preise NICHT gesetzt (Spalte non-nullable default 0).
                    'article_no' => $serienPraezise ? ($row['herst_artikelnr'] ?: null) : null,
                    'qty_offer' => $row['menge'],
                    'qty_measurement' => $row['menge'],
                    'qty_final' => $row['menge'],
                    'note' => $serienPraezise ? 'Heizkörper – serien-präzise' : 'Heizkörper – Regel-Kandidat (kein Preis/SKU)',
                    'raw_snapshot' => [
                        'herkunft' => 'heizkoerper',
                        'kategorie' => $row['kategorie'],
                        'datenqualitaet' => $result->datenqualitaet,
                        'begruendung' => $row['begruendung'],
                        'accessory_id' => $row['accessory_id'],
                        'preis_bekannt' => false,
                        'eingabe' => [
                            'vorlauf' => $data['vorlauf'] ?? null,
                            'spreizung' => $data['spreizung'] ?? null,
                            'anzahl' => $data['anzahl'] ?? null,
                            'ist_ventil_heizkoerper' => (bool) ($data['ist_ventil_heizkoerper'] ?? false),
                            'anschluss_fuehrung' => $data['anschluss_fuehrung'] ?? 'zweirohr',
                            'kopf_norm_bestand' => $data['kopf_norm_bestand'] ?? null,
                        ],
                    ],
                ]);
            }

            return $out;
        });

        return response()->json([
            'success' => true,
            'datenqualitaet' => $result->datenqualitaet,
            'section_title' => $section,
            'anzahl_positionen' => count($created),
        ], 201);
    }

    /** @return array{0: ?RadiatorSpec, 1: float, 2: int} [spec, baulaengeMm, anzahl] */
    private function resolveSpec(array $data): array
    {
        $baulaengeMm = isset($data['baulaenge_mm']) ? (float) $data['baulaenge_mm'] : null;
        $anzahl = (int) ($data['anzahl'] ?? 1);
        $spec = null;

        if (! empty($data['installation_id']) && ($inst = RadiatorInstallation::find($data['installation_id']))) {
            $spec = $inst->radiator_spec_id ? RadiatorSpec::find($inst->radiator_spec_id) : null;
            $baulaengeMm ??= ($inst->width !== null ? (float) $inst->width : null);
            if (! isset($data['anzahl'])) {
                $anzahl = max(1, (int) ($inst->anzahl ?? 1));
            }
        }
        if (! $spec && ! empty($data['radiator_spec_id'])) {
            $spec = RadiatorSpec::find($data['radiator_spec_id']);
        }

        return [$spec, $baulaengeMm ?? 1000.0, max(1, $anzahl)];
    }

    private function resolveSituation(array $data): RadiatorSituation
    {
        $fuehrung = $data['anschluss_fuehrung'] ?? 'zweirohr';
        $kopf = $data['kopf_norm_bestand'] ?? null;
        $hersteller = $data['hk_hersteller'] ?? null;

        if (! empty($data['installation_id']) && ($inst = RadiatorInstallation::find($data['installation_id']))) {
            $fuehrung = $inst->anschluss_fuehrung ?? $fuehrung;
            $kopf ??= $inst->kopf_norm_bestand;
            if ($inst->radiator_spec_id && ($spec = RadiatorSpec::find($inst->radiator_spec_id))) {
                $hersteller ??= $spec->hersteller;
            }
        }

        return new RadiatorSituation(
            istVentilHeizkoerper: (bool) ($data['ist_ventil_heizkoerper'] ?? false),
            anschlussFuehrung: $fuehrung,
            kopfNormBestand: $kopf,
            hkHersteller: $hersteller,
            hkSerie: $data['hk_serie'] ?? null,
            baujahr: isset($data['baujahr']) ? (int) $data['baujahr'] : null,
            heizlastW: isset($data['heizlast_w']) ? (float) $data['heizlast_w'] : null,
            spreizungK: isset($data['spreizung']) ? (float) $data['spreizung'] : null,
            dpVentilMbar: isset($data['dp_ventil_mbar']) ? (float) $data['dp_ventil_mbar'] : 100,
        );
    }
}
