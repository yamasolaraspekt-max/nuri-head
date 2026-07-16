<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Anforderungsprofil;
use App\Models\LeadAlternativeAdd;
use Illuminate\Http\Request;

/**
 * Gebäudeakte — Welle A4, V1 LESEND (2026-07-16, Spec: docs/planner-spec-gebaeudeakte.md).
 *
 * KEIN neues Schema, KEIN Schreibpfad: die Akte ist eine Sicht auf das kanonische Objekt
 * (LeadAlternativeAdd) — gepflegt wird weiter in der bestehenden Erfassung (Kundenakte → Objekt).
 * Kapitel-Vollständigkeit und Auslegungs-Reife machen fehlende Operanden sichtbar (Operanden-Gate:
 * Lücken zeigen, nie füllen).
 */
class ObjektakteController extends Controller
{
    /** Kapitel der Akte: Feld => Label (nur vorhandene Objekt-Spalten — eine Wahrheit). */
    public const KAPITEL = [
        'Standort' => [
            'street' => 'Straße', 'postcode' => 'PLZ', 'city' => 'Ort',
            'lat' => 'Breitengrad', 'lon' => 'Längengrad', 'elevation' => 'Geländehöhe',
        ],
        'Gebäudehülle' => [
            'house_year' => 'Baujahr', 'building_type' => 'Gebäudetyp', 'building_condition' => 'Zustand',
            'building_length' => 'Länge', 'building_width' => 'Breite', 'facade_height' => 'Fassadenhöhe',
            'total_window_area' => 'Fensterfläche', 'living_space' => 'Wohnfläche',
            'number_stories' => 'Geschosse', 'number_we' => 'Wohneinheiten',
        ],
        'Dach' => [
            'roof_type' => 'Dachform', 'roof_age' => 'Dachalter', 'roof_pitch' => 'Neigung',
            'roof_direction' => 'Ausrichtung', 'roof_covering' => 'Eindeckung',
        ],
        'Heizung' => [
            'heating_system_type' => 'Heizsystem', 'heating_system_year' => 'Baujahr Heizung',
            'old_heating_power' => 'Leistung Bestand', 'heat_distribution' => 'Wärmeverteilung',
            'flow_temperature' => 'Vorlauftemperatur',
        ],
        'Verbräuche' => [
            'annual_heating_energy_consumption' => 'Jahres-Heizenergie',
            'annual_heating_energy_consumption_kwh' => 'Jahres-Heizenergie (kWh)',
            'total_electricity_consumption' => 'Jahres-Strom',
            'electricity_price' => 'Strompreis', 'feed_in_tariff' => 'Einspeisevergütung',
        ],
    ];

    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $objekte = LeadAlternativeAdd::query()
            ->with('lead:id,firma,name,lastname,customer_no')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('street', 'like', "%{$q}%")
                        ->orWhere('city', 'like', "%{$q}%")
                        ->orWhere('postcode', 'like', "%{$q}%")
                        ->orWhere('object_name', 'like', "%{$q}%")
                        ->orWhereHas('lead', function ($l) use ($q) {
                            $l->where('name', 'like', "%{$q}%")
                                ->orWhere('lastname', 'like', "%{$q}%")
                                ->orWhere('firma', 'like', "%{$q}%");
                        });
                });
            })
            ->orderByDesc('id')
            ->paginate(25)
            ->appends($request->query());

        $rows = collect($objekte->items())->map(function (LeadAlternativeAdd $objekt) {
            return [
                'objekt' => $objekt,
                'kunde' => $this->kundenName($objekt),
                'ampel' => collect(self::KAPITEL)->map(fn ($felder) => $this->vollstaendigkeit($objekt, $felder)),
            ];
        });

        return view('admin.objekte.index', [
            'objekte' => $objekte,
            'rows' => $rows,
            'q' => $q,
            'kapitel' => array_keys(self::KAPITEL),
        ]);
    }

    public function show(int $alternativeId)
    {
        $objekt = LeadAlternativeAdd::query()->with('lead:id,firma,name,lastname,customer_no')->findOrFail($alternativeId);

        $profil = Anforderungsprofil::query()
            ->where('verankerbar_type', LeadAlternativeAdd::class)
            ->where('verankerbar_id', $objekt->id)
            ->where('status', Anforderungsprofil::STATUS_AKTIV)
            ->orderByDesc('version')
            ->first();

        $kapitel = collect(self::KAPITEL)->map(function ($felder, $name) use ($objekt) {
            $werte = [];
            $fehlt = [];
            foreach ($felder as $feld => $label) {
                $wert = $objekt->{$feld};
                if ($wert === null || $wert === '' || $wert === 0 || $wert === '0') {
                    $fehlt[] = $label;
                } else {
                    // Einheiten-Kante: Verbrauchswert nie ohne Einheit zeigen.
                    if ($feld === 'annual_heating_energy_consumption') {
                        $wert .= ' ' . ($objekt->heating_energy_unit ?: '⚠ Einheit fehlt');
                    }
                    $werte[$label] = $wert;
                }
            }

            return ['werte' => $werte, 'fehlt' => $fehlt, 'quote' => $this->vollstaendigkeit($objekt, $felder)];
        });

        return view('admin.objekte.akte', [
            'objekt' => $objekt,
            'kunde' => $this->kundenName($objekt),
            'kapitel' => $kapitel,
            'profil' => $profil,
            'reife' => $this->auslegungsReife($objekt, $profil),
        ]);
    }

    /** Anteil gefüllter Felder eines Kapitels in Prozent. */
    private function vollstaendigkeit(LeadAlternativeAdd $objekt, array $felder): int
    {
        $gefuellt = 0;
        foreach (array_keys($felder) as $feld) {
            $wert = $objekt->{$feld};
            if ($wert !== null && $wert !== '' && $wert !== 0 && $wert !== '0') {
                $gefuellt++;
            }
        }

        return (int) round(100 * $gefuellt / max(1, count($felder)));
    }

    /**
     * Auslegungs-Reife: welche Operanden der WpAuslegungsEingabe das Objekt heute liefert.
     * Es wird NICHTS gerechnet oder geraten — nur belegt/fehlt (Operanden-Gate).
     */
    private function auslegungsReife(LeadAlternativeAdd $objekt, ?Anforderungsprofil $profil): array
    {
        $hatHeizenergie = !empty($objekt->annual_heating_energy_consumption_kwh)
            || (!empty($objekt->annual_heating_energy_consumption) && !empty($objekt->heating_energy_unit));

        return [
            ['operand' => 'PLZ (Klima)', 'ok' => !empty($objekt->postcode), 'wert' => $objekt->postcode],
            ['operand' => 'Koordinaten (lat/lon)', 'ok' => !empty($objekt->lat) && !empty($objekt->lon), 'wert' => $objekt->lat ? $objekt->lat . ' / ' . $objekt->lon : null],
            ['operand' => 'Vorlauftemperatur', 'ok' => !empty($objekt->flow_temperature), 'wert' => $objekt->flow_temperature],
            ['operand' => 'Heizsystem / Wärmeverteilung', 'ok' => !empty($objekt->heat_distribution), 'wert' => $objekt->heat_distribution],
            ['operand' => 'Jahres-Heizenergie (qHeiz)', 'ok' => $hatHeizenergie, 'wert' => $objekt->annual_heating_energy_consumption_kwh ? $objekt->annual_heating_energy_consumption_kwh . ' kWh' : ($hatHeizenergie ? $objekt->annual_heating_energy_consumption . ' ' . $objekt->heating_energy_unit : null)],
            ['operand' => 'Warmwasser-Basis (Personen)', 'ok' => !empty($objekt->number_people), 'wert' => $objekt->number_people ? $objekt->number_people . ' Personen' : null],
            ['operand' => 'Heizlast (phiHl) aus Anforderungsprofil', 'ok' => $profil !== null, 'wert' => $profil ? 'Profil v' . $profil->version . ' aktiv — Ergebnis-Anbindung folgt mit dem Ketten-Adapter' : null],
        ];
    }

    private function kundenName(LeadAlternativeAdd $objekt): string
    {
        $lead = $objekt->lead;
        if (!$lead) {
            return '— ohne Kundenzuordnung —';
        }
        $name = trim((string) ($lead->firma ?? '')) !== ''
            ? $lead->firma
            : trim(($lead->name ?? '') . ' ' . ($lead->lastname ?? ''));

        return $name !== '' ? $name : '— ohne Kundenzuordnung —';
    }
}
