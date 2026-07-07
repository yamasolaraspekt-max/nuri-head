<?php

namespace App\Http\Controllers\Energie;

use App\Http\Controllers\Controller;
use App\Services\Heizlast\FussbodenheizungService;
use Illuminate\Http\Request;

/**
 * Fußbodenheizung-Schnellcheck (DIN EN 1264): aus Raumfläche (m²) und Heizlast (W)
 * prüfen, ob die Flächenheizung den Bedarf bei niedriger Vorlauftemperatur deckt.
 * Ergebnis: spezifische Leistung q [W/m²], Mindest-Vorlauftemperatur, Ampel-Status
 * und die Leistungstabelle über die gängigen Vorlauf-/Rücklaufpaare.
 *
 * Reiner server-gerenderter Durchstich über den portierten Kern
 * App\Services\Heizlast\FussbodenheizungService — keine eigene Physik hier.
 * Blade + Bootstrap/Vuexy + minimal jQuery. KEIN Alpine (CLAUDE.md).
 */
class FussbodenCheckController extends Controller
{
    /** Vollbenutzungsstunden Heizung (Überschlag, Richtwert). */
    private const VOLLBENUTZUNGSSTUNDEN = 1800;

    /** GET energie.fussboden-check → Eingabeformular, Ergebnis leer. */
    public function index()
    {
        return view('admin.energie.fussboden_check', [
            'eingabe' => $this->defaults(),
            'ergebnis' => null,
            'fehler' => null,
        ]);
    }

    /**
     * POST energie.fussboden-check.berechnen → validiert die FBH-Parameter, ruft
     * FussbodenheizungService::analyse() und liefert das Ergebnis. Bei AJAX/JSON-Wunsch
     * als JSON (wie im wberechnung-Vorbild), sonst server-gerendert als View.
     */
    public function berechnen(Request $request, FussbodenheizungService $svc)
    {
        $data = $request->validate([
            'flaeche_m2' => ['required', 'numeric', 'min:1', 'max:1000'],
            'heizlast_w' => ['required', 'numeric', 'min:10', 'max:300000'],
            'raumtemp_c' => ['nullable', 'numeric', 'between:10,30'],
            'vorlauf_c' => ['nullable', 'numeric', 'between:25,60'],
            'spreizung_k' => ['nullable', 'numeric', 'between:3,15'],
            'max_oberflaeche_c' => ['nullable', 'numeric', 'between:25,35'],
            'rohr_aussen_mm' => ['nullable', 'numeric', 'between:10,25'],
            'verlegeabstand_mm' => ['nullable', 'numeric', 'between:50,400'],
            'bodenbelag_r' => ['nullable', 'numeric', 'between:0,0.25'],
            'estrich_ueber_mm' => ['nullable', 'numeric', 'between:20,100'],
        ]);

        $flaeche = (float) $data['flaeche_m2'];
        $heizlast = (float) $data['heizlast_w'];
        $raumtemp = (float) ($data['raumtemp_c'] ?? 20);
        $vorlauf = (float) ($data['vorlauf_c'] ?? 35);
        $spreizung = (float) ($data['spreizung_k'] ?? 5);
        $maxOberflaeche = (float) ($data['max_oberflaeche_c'] ?? 29);

        $fbh = [
            'rohr_aussen_mm' => (float) ($data['rohr_aussen_mm'] ?? 16),
            'verlegeabstand_mm' => (float) ($data['verlegeabstand_mm'] ?? 100),
            'bodenbelag_r' => (float) ($data['bodenbelag_r'] ?? 0.0),
            'estrich_ueber_mm' => (float) ($data['estrich_ueber_mm'] ?? 45),
        ];

        $analyse = $svc->analyse($fbh, $flaeche, $heizlast, $raumtemp, $vorlauf, $spreizung, $maxOberflaeche);
        $tabelle = $svc->leistungstabelle($fbh, $raumtemp, $maxOberflaeche);

        $kwh = $heizlast * self::VOLLBENUTZUNGSSTUNDEN / 1000;
        $deckung = $heizlast > 0 ? ($analyse['q_real_w'] / $heizlast) : 0.0;

        $eingabe = [
            'flaeche_m2' => $flaeche,
            'heizlast_w' => (int) round($heizlast),
            'raumtemp_c' => $raumtemp,
            'vorlauf_c' => $vorlauf,
            'spreizung_k' => $spreizung,
            'max_oberflaeche_c' => $maxOberflaeche,
            'rohr_aussen_mm' => $fbh['rohr_aussen_mm'],
            'verlegeabstand_mm' => $fbh['verlegeabstand_mm'],
            'bodenbelag_r' => $fbh['bodenbelag_r'],
            'estrich_ueber_mm' => $fbh['estrich_ueber_mm'],
        ];

        $ergebnis = [
            'raum' => [
                'heizlast_w' => (int) round($heizlast),
                'bedarf_w_m2' => (int) round($flaeche > 0 ? $heizlast / $flaeche : 0),
                'kwh_a' => (int) round($kwh),
            ],
            'fbh' => array_merge($analyse, [
                'gedeckte_kwh_a' => (int) round(min(1.0, $deckung) * $kwh),
            ]),
            'tabelle' => $tabelle,
        ];

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($ergebnis);
        }

        return view('admin.energie.fussboden_check', [
            'eingabe' => $eingabe,
            'ergebnis' => $ergebnis,
            'fehler' => null,
        ]);
    }

    /** @return array<string, float|int> */
    private function defaults(): array
    {
        return [
            'flaeche_m2' => 20,
            'heizlast_w' => 1500,
            'raumtemp_c' => 20,
            'vorlauf_c' => 35,
            'spreizung_k' => 5,
            'max_oberflaeche_c' => 29,
            'rohr_aussen_mm' => 16,
            'verlegeabstand_mm' => 100,
            'bodenbelag_r' => 0.0,
            'estrich_ueber_mm' => 45,
        ];
    }
}
