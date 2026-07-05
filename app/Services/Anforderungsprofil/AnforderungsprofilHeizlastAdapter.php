<?php

namespace App\Services\Anforderungsprofil;

use App\Models\Anforderungsprofil;
use App\Models\AnforderungsprofilWert;
use App\Services\Heizlast\HeizlastRechner;
use Illuminate\Support\Collection;
use RuntimeException;

/**
 * B2a-3 — Adapter: bindet den byte-genau portierten HeizlastRechner (aus wberechnung@b4a9eda,
 * DIN EN 12831-1) an das Anforderungsprofil-Framework. Liest Bedarfswerte über die SchluesselRegistry
 * + die Geometrie-Spalte, baut den Array-Kontrakt des Kerns, ruft ihn, schreibt die Ergebnis-Aggregate
 * als Profil-Werte zurück (datenlage='berechnet').
 *
 * KERN-REGEL: der HeizlastRechner wird NICHT angefasst — der Adapter übersetzt, der Kern rechnet.
 * Datenlage-Durchreichung (W-B2a-4): geschätzte Eingaben + ungeprüfte/geschätzte U-Werte der Geometrie
 * landen im ergebnis_hinweis (der Kern selbst liest weiterhin nur u_wert, byte-genau).
 */
class AnforderungsprofilHeizlastAdapter
{
    /** Pflicht-Bedarfswerte (Operanden-Gate) — ohne diese keine Rechnung, keine stillen Kern-Defaults. */
    private const PFLICHT_WERTE = ['norm_aussentemp_c'];

    public function __construct(private HeizlastRechner $rechner) {}

    /**
     * @param  array{waermebruecken?: string}  $parameter  Rechenparameter mit dokumentierten Defaults
     * @return array<string, mixed> das rohe Kern-Ergebnis
     */
    public function berechneUndSchreibe(Anforderungsprofil $profil, array $parameter = []): array
    {
        $werte = $profil->werte()->get()->keyBy('schluessel');
        $geo = $profil->gebaeude_geometrie ?? [];

        $this->gate($werte, $geo);

        $wbModus = $parameter['waermebruecken'] ?? 'pauschal';

        $eingabe = [
            'norm_aussentemp_c' => (float) $werte['norm_aussentemp_c']->wert_num,
            'waermebruecken' => $wbModus,
            'komfortzuschlag_k' => $werte->has('komfortzuschlag_k') ? (float) $werte['komfortzuschlag_k']->wert_num : 0.0,
            'intermittierend' => $werte->has('intermittierend')
                ? filter_var($werte['intermittierend']->wert, FILTER_VALIDATE_BOOLEAN) : false,
            'raeume' => $geo['raeume'],
        ];

        $ergebnis = $this->rechner->berechne($eingabe); // <-- KERN, unberührt

        $this->rueckschreibung($profil, $ergebnis, $wbModus, $this->hinweise($werte, $geo));

        return $ergebnis;
    }

    /** Operanden-Gate: fehlende Pflicht-Eingaben → Rechnung verweigern mit konkreter Fehlliste. */
    private function gate(Collection $werte, array $geo): void
    {
        $fehlt = [];
        if (empty($geo['raeume'] ?? null)) {
            $fehlt[] = 'gebaeude_geometrie.raeume';
        }
        foreach (self::PFLICHT_WERTE as $k) {
            if (! $werte->has($k) || $werte[$k]->wert_num === null) {
                $fehlt[] = $k;
            }
        }
        if ($fehlt !== []) {
            throw new RuntimeException('Heizlast-Rechnung verweigert — fehlende Pflicht-Eingaben: '.implode(', ', $fehlt));
        }
    }

    /** Datenlage-Aggregation (W-B2a-4): geschätzte Bedarfswerte + ungeprüfte/geschätzte U-Werte → Hinweise. */
    private function hinweise(Collection $werte, array $geo): array
    {
        $h = [];
        foreach (['norm_aussentemp_c', 'komfortzuschlag_k'] as $k) {
            if ($werte->has($k) && $werte[$k]->datenlage === 'geschaetzt') {
                $h[] = "Eingabe {$k} geschätzt";
            }
        }
        $ungeprueft = 0;
        $geschaetzt = 0;
        foreach ($geo['raeume'] ?? [] as $raum) {
            foreach ($raum['bauteile'] ?? [] as $b) {
                $dl = $b['u_wert_datenlage'] ?? null;
                if ($dl === 'importiert_ungeprueft') {
                    $ungeprueft++;
                } elseif ($dl === 'geschaetzt') {
                    $geschaetzt++;
                }
            }
        }
        if ($ungeprueft > 0) {
            $h[] = "{$ungeprueft} U-Wert(e) importiert_ungeprueft";
        }
        if ($geschaetzt > 0) {
            $h[] = "{$geschaetzt} U-Wert(e) geschätzt";
        }

        return $h;
    }

    /** Rückschreibung auf die übergebene (aktive) Version via updateOrCreate → Registry-Hook greift; keine neue Version. */
    private function rueckschreibung(Anforderungsprofil $profil, array $ergebnis, string $wbModus, array $hinweise): void
    {
        $g = $ergebnis['gebaeude'];
        $this->wert($profil, 'phi_hl_kw', $g['auslegungsheizlast_kw'], 'kW', 'berechnet', 'HeizlastRechner');
        $this->wert($profil, 'standardheizlast_kw', $g['standardheizlast_kw'], 'kW', 'berechnet', 'HeizlastRechner');
        $this->wert($profil, 'spezifische_heizlast_w_m2', $g['spezifische_heizlast_w_m2'], 'W/m²', 'berechnet', 'HeizlastRechner');
        // Rechenparameter ehrlich ausweisen (womit wurde gerechnet)
        $this->wert($profil, 'waermebruecken', $wbModus, null, 'geschaetzt', 'Default', num: false);
        if ($hinweise !== []) {
            $this->wert($profil, 'ergebnis_hinweis', 'enthält geschätzte/ungeprüfte Eingaben: '.implode(', ', $hinweise), null, 'berechnet', 'HeizlastRechner', num: false);
        }
    }

    private function wert(Anforderungsprofil $profil, string $schluessel, mixed $wert, ?string $einheit, string $datenlage, string $quelle, bool $num = true): void
    {
        AnforderungsprofilWert::updateOrCreate(
            ['anforderungsprofil_id' => $profil->getKey(), 'schluessel' => $schluessel],
            [
                'wert' => (string) $wert,
                'wert_num' => $num ? (float) $wert : null,
                'einheit' => $einheit,
                'datenlage' => $datenlage,
                'quelle' => $quelle,
                'erfassungsweg' => 'berechnet',
            ],
        );
    }
}
