<?php

namespace App\Services\Heizlast;

use App\Models\Baualtersklasse;
use App\Models\Konstruktion;
use App\Models\Material;

/**
 * U-Wert-Ermittlung für die Heizlast (DIN EN 12831-1) über drei Eingabe-Strategien,
 * jeweils mit Konfidenz und Quelle:
 *   A „bekannt"     – U direkt aus Baubeschreibung/Energieausweis (Konfidenz hoch).
 *   B „konstruktiv" – Schichtaufbau U = 1/(Rsi + Σ d/λ + Rse), λ aus DIN 4108-4,
 *                     Rsi/Rse nach DIN EN ISO 6946 (Konfidenz hoch bei DB-λ, sonst mittel).
 *   C „Baualter"    – typische U-Werte je Baualtersklasse/Sanierungsstufe (Konfidenz niedrig).
 *
 * Fenster/Türen werden nicht über λ-Schichten, sondern als Uw-Typwerte geführt
 * (DIN EN ISO 10077, Richtwerte).
 */
class UWertService
{
    /** Wärmeübergangswiderstände Rsi/Rse (m²K/W) je Bauteiltyp nach DIN EN ISO 6946. */
    private const RSI_RSE = [
        'wand' => [0.13, 0.04],   // horizontaler Wärmestrom
        'dach' => [0.10, 0.04],   // aufwärts
        'decke' => [0.10, 0.04],  // oberste Geschossdecke, aufwärts
        'boden' => [0.17, 0.00],  // abwärts, gegen Erdreich/unbeheizt
    ];

    /** Fenster-Uw je Typ (W/m²K, Richtwerte nach DIN EN ISO 10077). */
    private const FENSTER_U = [
        'einfachglas' => 5.0, '2fach_alt' => 2.8, '2fach' => 1.3, '3fach' => 0.8,
    ];

    /** Tür-U je Typ (W/m²K, Richtwerte). */
    private const TUER_U = [
        'alt_holz' => 3.0, 'standard' => 1.8, 'modern_gedaemmt' => 1.2,
    ];

    /**
     * Strategie A – bekannter U-Wert.
     *
     * @return array<string, mixed>
     */
    public function bekannt(float $u, ?string $quelle = null): array
    {
        return $this->ergebnis($u, 'A', 'hoch', $quelle ?: 'Direkteingabe (Baubeschreibung/Energieausweis)');
    }

    /**
     * Strategie B – Schichtaufbau. Jede Schicht: ['material' => Name] oder ['lambda' => W/mK],
     * dazu 'dicke_mm'. Materialnamen werden gegen die DIN-4108-4-Datenbank aufgelöst.
     *
     * @param  array<int, array{material?: string, lambda?: float, name?: string, dicke_mm: float|int}>  $schichten
     * @return array<string, mixed>
     */
    public function ausSchichten(array $schichten, string $bauteiltyp): array
    {
        [$rsi, $rse] = self::RSI_RSE[$bauteiltyp] ?? self::RSI_RSE['wand'];

        $r = 0.0;
        $alleAusDb = true;
        $aufbau = [];
        foreach ($schichten as $s) {
            $d = (float) ($s['dicke_mm'] ?? 0) / 1000.0;
            $lambda = null;
            $name = null;
            $ausDb = false;
            if (! empty($s['material'])) {
                $m = Material::where('name', $s['material'])->first();
                if ($m) {
                    $lambda = $m->lambda_w_mk;
                    $name = $m->name;
                    $ausDb = true;
                }
            }
            if ($lambda === null && isset($s['lambda'])) {
                $lambda = (float) $s['lambda'];
                $name = $s['name'] ?? ($s['material'] ?? '(λ manuell)');
            }
            if ($lambda === null || $lambda <= 0 || $d <= 0) {
                continue;
            }
            if (! $ausDb) {
                $alleAusDb = false;
            }
            $rSchicht = $d / $lambda;
            $r += $rSchicht;
            $aufbau[] = ['name' => $name, 'dicke_mm' => $s['dicke_mm'] ?? null, 'lambda' => $lambda, 'r' => round($rSchicht, 4)];
        }

        $u = $r > 0 ? 1.0 / ($rsi + $r + $rse) : 0.0;

        return $this->ergebnis(
            $u, 'B', $alleAusDb && $aufbau !== [] ? 'hoch' : 'mittel',
            'Schichtaufbau (DIN EN ISO 6946), λ nach DIN 4108-4',
            ['rsi' => $rsi, 'rse' => $rse, 'r_schichten' => round($r, 4), 'aufbau' => $aufbau],
        );
    }

    /**
     * Strategie C – U-Wert aus Baualtersklasse/Sanierungsstufe.
     *
     * @return array<string, mixed>|null
     */
    public function ausBaualter(int $baujahr, string $bauteiltyp, string $sanierung = 'unsaniert'): ?array
    {
        $k = $this->klasse($baujahr, $sanierung) ?? $this->klasse($baujahr, 'unsaniert');
        if (! $k) {
            return null;
        }

        $u = $k->{'u_'.$bauteiltyp} ?? null;
        if ($u === null) {
            return null;
        }

        return $this->ergebnis((float) $u, 'C', 'niedrig', $k->quelle, [
            'baujahr' => $baujahr,
            'klasse' => $k->von_jahr.'–'.$k->bis_jahr,
            'sanierung' => $k->sanierungsstufe,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function fenster(string $typ): array
    {
        return $this->ergebnis(self::FENSTER_U[$typ] ?? self::FENSTER_U['2fach'], 'A', 'hoch', 'Fenster-Uw (DIN EN ISO 10077, Richtwert)');
    }

    /**
     * @return array<string, mixed>
     */
    public function tuer(string $typ): array
    {
        return $this->ergebnis(self::TUER_U[$typ] ?? self::TUER_U['standard'], 'A', 'hoch', 'Tür-U (Richtwert)');
    }

    /**
     * Fenster/Tür mit direktem U-Wert (z. B. Produkt-Datenblatt oder Sanierungs-Override),
     * statt über den Typ-Schlüssel. Konfidenz „hoch".
     *
     * @return array<string, mixed>
     */
    public function fensterDirekt(float $u, ?string $quelle = null): array
    {
        return $this->ergebnis($u, 'A', 'hoch', $quelle ?: 'Fenster-U direkt (Produkt/Datenblatt)');
    }

    /**
     * U-Wert eines wiederverwendbaren Aufbaus – delegiert an die bestehende
     * Schicht-Engine ausSchichten(). Materialien werden per material_id aufgelöst
     * (DB-λ → Konfidenz hoch), lambda_override hat Vorrang (Konfidenz mittel).
     *
     * @return array<string, mixed>
     */
    public function ausKonstruktion(Konstruktion $konstruktion): array
    {
        $schichten = [];
        foreach ($konstruktion->schichten ?? [] as $s) {
            $eintrag = ['dicke_mm' => $s['dicke_mm'] ?? 0];
            $override = $s['lambda_override'] ?? null;
            $material = ! empty($s['material_id']) ? Material::find($s['material_id']) : null;

            if ($override !== null && $override !== '') {
                $eintrag['lambda'] = (float) $override;
                $eintrag['name'] = $material?->name ?? '(λ manuell)';
            } elseif ($material !== null) {
                $eintrag['material'] = $material->name; // ausSchichten löst λ aus der DB auf
            }
            $schichten[] = $eintrag;
        }

        return $this->ausSchichten($schichten, $konstruktion->typ->bauteiltyp());
    }

    private function klasse(int $baujahr, string $sanierung): ?Baualtersklasse
    {
        return Baualtersklasse::where('von_jahr', '<=', $baujahr)
            ->where('bis_jahr', '>=', $baujahr)
            ->where('sanierungsstufe', $sanierung)
            ->first();
    }

    /**
     * @param  array<string, mixed>  $extra
     * @return array<string, mixed>
     */
    private function ergebnis(float $u, string $strategie, string $konfidenz, string $quelle, array $extra = []): array
    {
        return array_merge([
            'u_wert' => round($u, 3),
            'eingabe_strategie' => $strategie,
            'konfidenz' => $konfidenz,
            'quelle' => $quelle,
        ], $extra);
    }
}
