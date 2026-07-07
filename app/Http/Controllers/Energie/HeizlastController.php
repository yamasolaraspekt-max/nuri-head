<?php

namespace App\Http\Controllers\Energie;

use App\Http\Controllers\Controller;
use App\Models\HeizlastBauteil;
use App\Models\HeizlastProjekt;
use App\Models\HeizlastRaum;
use App\Services\Heizlast\HeizlastProjektService;
use App\Services\Heizlast\WaermepumpenMatchService;
use App\Services\Klima\KlimaPlzService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Ist-Heizlast-Rechner (Strang Energie).
 *
 * Reduzierte Variante des Sanierungs-Rechners (SanierungController) OHNE Maßnahmen/
 * Sanierung: nur die reine IST-Heizlast eines Gebäudes. Server-gerendert (Blade +
 * Bootstrap/Vuexy + minimal jQuery, KEIN Alpine — CLAUDE.md).
 *
 * Baut aus dem Formular ein TRANSIENTES Einzonen-Heizlast-Projekt (Gebäude = 1 Raum)
 * mit Hüllbauteilen, ruft den portierten Kern HeizlastProjektService::fuerProjekt()
 * und verwirft das Projekt danach wieder (Cascade-Delete). Rechner-Tool — keine
 * Projekt-Persistenz. Optional: passende Luft/Wasser-Wärmepumpen als Vorschlags-Block.
 */
class HeizlastController extends Controller
{
    /** Auswählbare Bauteil-Typen (Formular-Vokabular). */
    private const BAUTEIL_TYPEN = ['wand', 'dach', 'decke', 'boden', 'fenster', 'tuer'];

    /** Auswählbare Grenzflächen eines Bauteils. */
    private const GRENZFLAECHEN = ['aussen', 'erdreich', 'unbeheizt'];

    public function __construct(
        private HeizlastProjektService $service,
        private KlimaPlzService $klimaPlz,
        private WaermepumpenMatchService $wpMatch,
    ) {}

    /** GET energie.heizlast → leeres Eingabeformular. */
    public function index()
    {
        return view('admin.energie.heizlast', [
            'bauteilTypen' => self::BAUTEIL_TYPEN,
            'grenzflaechen' => self::GRENZFLAECHEN,
            'alt' => [],
            'ergebnis' => null,
            'wp' => null,
            'fehler' => null,
        ]);
    }

    /** POST energie.heizlast.berechnen → server-gerendertes Ist-Heizlast-Ergebnis. */
    public function berechnen(Request $request)
    {
        $data = $this->validieren($request);

        try {
            [$ergebnis, $wp] = $this->heizlastErgebnis($data);
        } catch (Throwable $e) {
            return view('admin.energie.heizlast', [
                'bauteilTypen' => self::BAUTEIL_TYPEN,
                'grenzflaechen' => self::GRENZFLAECHEN,
                'alt' => $request->all(),
                'ergebnis' => null,
                'wp' => null,
                'fehler' => 'Berechnung fehlgeschlagen: '.$e->getMessage(),
            ]);
        }

        return view('admin.energie.heizlast', [
            'bauteilTypen' => self::BAUTEIL_TYPEN,
            'grenzflaechen' => self::GRENZFLAECHEN,
            'alt' => $request->all(),
            'ergebnis' => $ergebnis,
            'wp' => $wp,
            'fehler' => null,
        ]);
    }

    /**
     * Validierung des Formulars (nur Gebäude/Raum/Bauteile — keine Maßnahmen).
     *
     * @return array<string, mixed>
     */
    private function validieren(Request $request): array
    {
        $typen = implode(',', self::BAUTEIL_TYPEN);
        $grenz = implode(',', self::GRENZFLAECHEN);

        return $request->validate([
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
        ]);
    }

    /**
     * Baut TRANSIENT Projekt/Raum/Bauteile genau nach Contract, rechnet die IST-Heizlast
     * und verwirft das Projekt danach wieder (Cascade). Ermittelt optional passende
     * Luft/Wasser-Wärmepumpen zur Auslegungsheizlast.
     *
     * @param  array<string, mixed>  $data
     * @return array{0: array<string, mixed>, 1: array<string, mixed>|null}
     */
    private function heizlastErgebnis(array $data): array
    {
        $projektIn = $data['projekt'] ?? [];
        $raumIn = $data['raum'];
        $bauteileIn = $data['bauteile'];

        $plz = (string) ($projektIn['standort_plz'] ?? '');
        $normAussentemp = ($plz !== '' ? $this->klimaPlz->getNormAussentempForPlz($plz) : null) ?? -8.5;

        $zielVorlauf = $projektIn['ziel_vorlauf_c'] ?? 55;
        $spreizung = $projektIn['spreizung_k'] ?? 7;

        $projekt = null;

        $ergebnis = DB::transaction(function () use (
            &$projekt, $projektIn, $raumIn, $bauteileIn, $plz, $normAussentemp, $zielVorlauf, $spreizung
        ) {
            $projekt = HeizlastProjekt::create([
                'name' => 'Ist-Heizlast-Rechner',
                'standort_plz' => $plz !== '' ? $plz : null,
                'norm_aussentemp_c' => $normAussentemp,
                'baujahr' => $projektIn['baujahr'] ?? null,
                'sanierungsstufe' => 'unsaniert',
                'ziel_vorlauf_c' => $zielVorlauf,
                'spreizung_k' => $spreizung,
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

            $res = $this->service->fuerProjekt($projekt->fresh());

            // TRANSIENT: Rechner-Tool — Projekt (Cascade: Raum/Bauteile) wieder verwerfen.
            $projekt->delete();

            return $res;
        });

        // Optional: passende Luft/Wasser-Wärmepumpen zur Auslegungsheizlast.
        $wp = null;
        $heizlastKw = (float) ($ergebnis['gebaeude']['auslegungsheizlast_kw'] ?? 0);
        if ($heizlastKw > 0) {
            $wp = $this->wpMatch->kandidaten($heizlastKw, 'luft_wasser', 'heizkoerper', (float) $zielVorlauf);
        }

        return [$ergebnis, $wp];
    }
}
