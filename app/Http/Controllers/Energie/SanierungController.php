<?php

namespace App\Http\Controllers\Energie;

use App\Http\Controllers\Controller;
use App\Models\HeizlastBauteil;
use App\Models\HeizlastProjekt;
use App\Models\HeizlastRaum;
use App\Models\SanierungsVariante;
use App\Services\Heizlast\SanierungsWirtschaftlichkeitService;
use App\Services\Klima\KlimaPlzService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Sanierungs-Wirtschaftlichkeits-Rechner (Strang Energie).
 *
 * Server-gerenderter Durchstich (Blade + Bootstrap/Vuexy + minimal jQuery, KEIN Alpine).
 * Baut aus dem Formular ein TRANSIENTES Einzonen-Heizlast-Projekt (Gebäude = 1 Raum) mit
 * Hüllbauteilen und einer Sanierungs-Variante, ruft den portierten Kern
 * SanierungsWirtschaftlichkeitService::berechneVergleich() und verwirft das Projekt danach
 * wieder (Cascade-Delete). Es ist ein Rechner-Tool — keine Projekt-Persistenz-Verwaltung.
 */
class SanierungController extends Controller
{
    /** Auswählbare Bauteil-Typen (Formular- und Maßnahmen-Ziel-Vokabular). */
    private const BAUTEIL_TYPEN = ['wand', 'dach', 'decke', 'boden', 'fenster', 'tuer'];

    /** Auswählbare Grenzflächen eines Bauteils. */
    private const GRENZFLAECHEN = ['aussen', 'erdreich', 'unbeheizt'];

    public function __construct(
        private SanierungsWirtschaftlichkeitService $service,
        private KlimaPlzService $klimaPlz,
    ) {}

    /** GET energie.sanierung → leeres Eingabeformular. */
    public function index()
    {
        return view('admin.energie.sanierung', [
            'bauteilTypen' => self::BAUTEIL_TYPEN,
            'grenzflaechen' => self::GRENZFLAECHEN,
            'alt' => [],
            'ergebnis' => null,
            'fehler' => null,
        ]);
    }

    /** POST energie.sanierung.berechnen → server-gerendertes Vorher/Nachher-Ergebnis. */
    public function berechnen(Request $request)
    {
        $data = $this->validieren($request);

        try {
            $ergebnis = $this->sanierungErgebnis($data);
        } catch (Throwable $e) {
            return view('admin.energie.sanierung', [
                'bauteilTypen' => self::BAUTEIL_TYPEN,
                'grenzflaechen' => self::GRENZFLAECHEN,
                'alt' => $request->all(),
                'ergebnis' => null,
                'fehler' => 'Berechnung fehlgeschlagen: '.$e->getMessage(),
            ]);
        }

        return view('admin.energie.sanierung', [
            'bauteilTypen' => self::BAUTEIL_TYPEN,
            'grenzflaechen' => self::GRENZFLAECHEN,
            'alt' => $request->all(),
            'ergebnis' => $ergebnis,
            'fehler' => null,
        ]);
    }

    /** POST energie.sanierung.dokument → dieselbe Rechnung, Dokument-/Druck-View (anderer Agent). */
    public function dokument(Request $request)
    {
        $data = $this->validieren($request);

        try {
            $ergebnis = $this->sanierungErgebnis($data);
        } catch (Throwable $e) {
            return view('admin.energie.sanierung', [
                'bauteilTypen' => self::BAUTEIL_TYPEN,
                'grenzflaechen' => self::GRENZFLAECHEN,
                'alt' => $request->all(),
                'ergebnis' => null,
                'fehler' => 'Berechnung fehlgeschlagen: '.$e->getMessage(),
            ]);
        }

        return view('admin.energie.sanierung_dokument', [
            'ergebnis' => $ergebnis,
        ]);
    }

    /**
     * Gemeinsame Validierung für Berechnung und Dokument.
     *
     * @return array<string, mixed>
     */
    private function validieren(Request $request): array
    {
        $typen = implode(',', self::BAUTEIL_TYPEN);
        $grenz = implode(',', self::GRENZFLAECHEN);

        return $request->validate([
            'projekt.name' => ['nullable', 'string', 'max:190'],
            'projekt.standort_plz' => ['nullable', 'string', 'max:10'],
            'projekt.baujahr' => ['nullable', 'integer', 'min:1800', 'max:2100'],
            'projekt.ziel_vorlauf_c' => ['nullable', 'numeric', 'min:20', 'max:90'],
            'projekt.spreizung_k' => ['nullable', 'numeric', 'min:1', 'max:30'],

            'raum.grundflaeche_m2' => ['required', 'numeric', 'min:1'],
            'raum.hoehe_m' => ['required', 'numeric', 'min:1', 'max:10'],
            'raum.theta_int_c' => ['nullable', 'numeric', 'min:10', 'max:30'],
            'raum.luftwechsel_1h' => ['nullable', 'numeric', 'min:0', 'max:5'],

            'bauteile' => ['required', 'array', 'min:1'],
            'bauteile.*.typ' => ['required', "in:$typen"],
            'bauteile.*.grenzflaeche' => ['required', "in:$grenz"],
            'bauteile.*.flaeche_m2' => ['required', 'numeric', 'min:0.1'],
            'bauteile.*.u_wert' => ['required', 'numeric', 'min:0.01', 'max:10'],

            'massnahmen' => ['nullable', 'array'],
            'massnahmen.*.ziel_typ' => ['required_with:massnahmen', "in:$typen"],
            'massnahmen.*.u_neu' => ['required_with:massnahmen', 'numeric', 'min:0.01', 'max:10'],
            'massnahmen.*.kosten_brutto' => ['required_with:massnahmen', 'numeric', 'min:0'],

            'annahmen.energiepreis_ct_kwh' => ['nullable', 'numeric', 'min:0'],
            'annahmen.isfp' => ['nullable'],
            'annahmen.anzahl_we' => ['nullable', 'integer', 'min:1'],
        ]);
    }

    /**
     * Baut das TRANSIENTE Projekt/Raum/Bauteile/Variante genau nach Contract, rechnet den
     * Vergleich und verwirft das Projekt danach wieder (Cascade). Geteilt von berechnen()
     * und dokument().
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function sanierungErgebnis(array $data): array
    {
        $projektIn = $data['projekt'] ?? [];
        $raumIn = $data['raum'];
        $bauteileIn = $data['bauteile'];
        $massnahmenIn = $data['massnahmen'] ?? [];
        $annahmenIn = $data['annahmen'] ?? [];

        $plz = (string) ($projektIn['standort_plz'] ?? '');
        $normAussentemp = ($plz !== '' ? $this->klimaPlz->getNormAussentempForPlz($plz) : null) ?? -8.5;

        $projekt = null;

        return DB::transaction(function () use (
            &$projekt, $projektIn, $raumIn, $bauteileIn, $massnahmenIn, $annahmenIn, $plz, $normAussentemp
        ) {
            $projekt = HeizlastProjekt::create([
                'name' => $projektIn['name'] ?? 'Sanierungs-Rechner',
                'standort_plz' => $plz !== '' ? $plz : null,
                'norm_aussentemp_c' => $normAussentemp,
                'baujahr' => $projektIn['baujahr'] ?? null,
                'sanierungsstufe' => 'unsaniert',
                'ziel_vorlauf_c' => $projektIn['ziel_vorlauf_c'] ?? null,
                'spreizung_k' => $projektIn['spreizung_k'] ?? null,
            ]);

            $raum = HeizlastRaum::create([
                'heizlast_projekt_id' => $projekt->getKey(),
                'name' => 'Gebäude',
                'nutzung' => 'wohnen',
                'theta_int_c' => $raumIn['theta_int_c'] ?? null,
                'grundflaeche_m2' => $raumIn['grundflaeche_m2'],
                'hoehe_m' => $raumIn['hoehe_m'],
                'luftwechsel_1h' => $raumIn['luftwechsel_1h'] ?? null,
            ]);

            foreach ($bauteileIn as $b) {
                HeizlastBauteil::create([
                    'heizlast_raum_id' => $raum->getKey(),
                    'typ' => $b['typ'],
                    'grenzflaeche' => $b['grenzflaeche'],
                    'flaeche_m2' => $b['flaeche_m2'],
                    'u_strategie' => 'A',
                    'u_wert' => $b['u_wert'],
                ]);
            }

            $massnahmen = [];
            foreach ($massnahmenIn as $m) {
                $massnahmen[] = [
                    'ziel' => ['typ' => $m['ziel_typ']],
                    'u_neu' => (float) $m['u_neu'],
                    'kosten_brutto' => (float) $m['kosten_brutto'],
                ];
            }

            $variante = SanierungsVariante::create([
                'heizlast_projekt_id' => $projekt->getKey(),
                'label' => 'Sanierung',
                'massnahmen' => $massnahmen,
            ]);

            $ergebnis = $this->service->berechneVergleich($projekt->fresh(), $variante, [
                'energiepreis_ct_kwh' => (float) ($annahmenIn['energiepreis_ct_kwh'] ?? 12.0),
                'isfp' => (bool) ($annahmenIn['isfp'] ?? false),
                'anzahl_we' => (int) ($annahmenIn['anzahl_we'] ?? 1),
            ]);

            // TRANSIENT: Rechner-Tool — Projekt (Cascade: Raum/Bauteile/Variante) wieder verwerfen.
            $projekt->delete();

            return $ergebnis;
        });
    }
}
