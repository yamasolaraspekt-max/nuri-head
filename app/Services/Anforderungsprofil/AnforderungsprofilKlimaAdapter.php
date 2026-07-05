<?php

namespace App\Services\Anforderungsprofil;

use App\Models\Anforderungsprofil;
use App\Models\AnforderungsprofilWert;
use App\Services\Heizlast\HeizlastEingabe;
use App\Services\Heizlast\HoehenkorrekturService;
use App\Services\Heizlast\KlimaPlzService;
use App\Services\Heizlast\WarmwasserService;
use RuntimeException;

/**
 * B2b-C — Klima-/WW-Adapter: leitet die Norm-Außentemperatur θe (standort_plz → KlimaPlz-CSV,
 * + gelaendehoehe_m → Höhenkorrektur) und den Warmwasser-Bedarf ab und schreibt sie ins Profil zurück.
 * Damit **schließt dieser Adapter das Operanden-Gate des B2a-3-Heizlast-Adapters** (der norm_aussentemp_c
 * als Pflicht fordert). Byte-genau portierte Kerne (wb@b4a9eda) unberührt — der Adapter übersetzt.
 *
 * KlimaPlz liest database/data/klima_plz.csv (rechnende Wahrheit; Umstellung auf klima_plz-Tabelle =
 * Tag-X-Posten im Manifest). Identität CSV == Tabelle ist per Test belegt (Auflage C1.1).
 */
class AnforderungsprofilKlimaAdapter
{
    public function __construct(
        private KlimaPlzService $klimaPlz,
        private HoehenkorrekturService $hoehe,
        private WarmwasserService $ww,
    ) {}

    /** @return array<string, mixed> */
    public function berechneUndSchreibe(Anforderungsprofil $profil): array
    {
        $werte = $profil->werte()->get()->keyBy('schluessel');

        // Operanden-Gate: standort_plz Pflicht (keine stille Klima-Annahme)
        if (! $werte->has('standort_plz') || trim((string) $werte['standort_plz']->wert) === '') {
            throw new RuntimeException('Klima-Ableitung verweigert — fehlende Pflicht-Eingabe: standort_plz');
        }
        $plz = (string) $werte['standort_plz']->wert;

        $klima = $this->klimaPlz->lookup($plz);
        if ($klima === null) {
            throw new RuntimeException("Klima-Ableitung verweigert — PLZ {$plz} nicht in klima_plz.");
        }

        // Höhenkorrektur: Standorthöhe (Profil) gegen Bezugshöhe (CSV) — Kern rechnet, Adapter übersetzt.
        $standorthoehe = $werte->has('gelaendehoehe_m') ? (float) $werte['gelaendehoehe_m']->wert_num : null;
        $bezugshoehe = $klima['hoehe_m'] !== null ? (float) $klima['hoehe_m'] : null;
        $korr = $this->hoehe->korrigiere((float) $klima['nat_c'], $standorthoehe, $bezugshoehe);

        // Rückschreibung: norm_aussentemp_c schließt das B2a-3-Gate
        $quelle = 'klima_plz PLZ '.$plz.($korr['korrigiert'] ? ' + Höhenkorrektur' : '');
        $this->wert($profil, 'norm_aussentemp_c', $korr['norm_aussentemp_c'], '°C', 'berechnet', $quelle);

        // Warmwasser (nur wenn Personenzahl vorliegt — sonst kein stiller Default)
        $phiWw = null;
        if ($werte->has('personen_im_haushalt') && $werte['personen_im_haushalt']->wert_num !== null) {
            $e = HeizlastEingabe::fromArray([
                'personen_im_haushalt' => (int) $werte['personen_im_haushalt']->wert_num,
                'ww_mit_wp' => true,
            ]);
            $phiWw = $this->ww->phiWwKw($e);
            $this->wert($profil, 'phi_ww_kw', $phiWw, 'kW', 'berechnet', 'WarmwasserService');
        }

        return ['norm_aussentemp_c' => $korr['norm_aussentemp_c'], 'phi_ww_kw' => $phiWw, 'hoehenkorrektur' => $korr, 'klima' => $klima];
    }

    private function wert(Anforderungsprofil $profil, string $schluessel, mixed $wert, ?string $einheit, string $datenlage, string $quelle): void
    {
        AnforderungsprofilWert::updateOrCreate(
            ['anforderungsprofil_id' => $profil->getKey(), 'schluessel' => $schluessel],
            [
                'wert' => (string) $wert,
                'wert_num' => (float) $wert,
                'einheit' => $einheit,
                'datenlage' => $datenlage,
                'quelle' => $quelle,
                'erfassungsweg' => 'berechnet',
            ],
        );
    }
}
