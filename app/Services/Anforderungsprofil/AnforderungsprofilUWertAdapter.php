<?php

namespace App\Services\Anforderungsprofil;

use App\Models\Anforderungsprofil;
use App\Services\Heizlast\UWertService;
use RuntimeException;

/**
 * B2b-A — UWert-Adapter: leitet fehlende Bauteil-U-Werte aus baujahr/sanierungsstufe ab
 * (UWertService::ausBaualter → baualtersklassen, Strategie C „Baualter") und schreibt sie mit
 * `u_wert_datenlage='tabula_richtwert'` in die Geometrie-Bauteile — genau die Datenlage-Durchreichung
 * (W-B2a-4), die der B2a-3-Heizlast-Adapter im ergebnis_hinweis aggregiert.
 *
 * Byte-genau portierter UWertService unberührt. Bauteile mit bereits gesetztem u_wert bleiben unangetastet
 * (kein Überschreiben verifizierter Werte). Der Adapter braucht nur Baualtersklasse — nicht FensterSpec.
 */
class AnforderungsprofilUWertAdapter
{
    public function __construct(private UWertService $uwert) {}

    /** @return array<string, mixed> */
    public function berechneUndSchreibe(Anforderungsprofil $profil): array
    {
        $werte = $profil->werte()->get()->keyBy('schluessel');
        $geo = $profil->gebaeude_geometrie ?? [];

        // Operanden-Gate: baujahr Pflicht (für Strategie C), Geometrie Pflicht
        if (! $werte->has('baujahr') || $werte['baujahr']->wert_num === null) {
            throw new RuntimeException('U-Wert-Ableitung verweigert — fehlende Pflicht-Eingabe: baujahr');
        }
        if (empty($geo['raeume'] ?? null)) {
            throw new RuntimeException('U-Wert-Ableitung verweigert — fehlende Geometrie (gebaeude_geometrie.raeume).');
        }

        $baujahr = (int) $werte['baujahr']->wert_num;
        $sanierung = $werte->has('sanierungsstufe') ? (string) $werte['sanierungsstufe']->wert : 'unsaniert';

        $ergaenzt = 0;
        foreach ($geo['raeume'] as $ri => $raum) {
            foreach ($raum['bauteile'] ?? [] as $bi => $b) {
                if (isset($b['u_wert']) && $b['u_wert'] !== null) {
                    continue; // vorhandenen (verifizierten/importierten) Wert nicht überschreiben
                }
                $r = $this->uwert->ausBaualter($baujahr, $this->bauteiltyp($b['typ'] ?? 'wand'), $sanierung);
                if ($r !== null) {
                    $geo['raeume'][$ri]['bauteile'][$bi]['u_wert'] = $r['u_wert'];
                    $geo['raeume'][$ri]['bauteile'][$bi]['u_wert_datenlage'] = 'tabula_richtwert';
                    $geo['raeume'][$ri]['bauteile'][$bi]['u_wert_quelle'] = $r['quelle'] ?? 'baualtersklassen';
                    $ergaenzt++;
                }
            }
        }

        $profil->gebaeude_geometrie = $geo;
        $profil->save();

        return ['ergaenzt' => $ergaenzt, 'geometrie' => $geo];
    }

    /** Geometrie-Bauteiltyp → baualtersklassen-Spalte (u_wand/u_dach/u_boden/u_fenster/u_tuer). */
    private function bauteiltyp(string $typ): string
    {
        return match ($typ) {
            'dach', 'decke' => 'dach',
            'boden' => 'boden',
            'fenster' => 'fenster',
            'tuer' => 'tuer',
            default => 'wand',
        };
    }
}
