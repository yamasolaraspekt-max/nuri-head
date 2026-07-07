<?php

namespace App\Services\Energie;

use App\Services\Energie\Contracts\SizingBattery;
use App\Services\Energie\Contracts\SizingInverter;
use App\Services\Energie\Contracts\SizingModule;
use App\Services\StringBuilderService;

/**
 * Projekt-Auslegung über mehrere Dachflächen mit je eigenem Modul + Anzahl gegen EINEN
 * Wechselrichter. Spannungs-/Strom-/Sicherungsregeln je Dach/String, Überdimensionierung,
 * Netz/Temperatur/Batterie global. Liefert Freigeben (grün/gelb) oder Alarm (rot).
 */
class PvProjektService
{
    public function __construct(
        private InverterSizingService $engine,
        private StringBuilderService $builder,
    ) {}

    /**
     * @param  array<int, array{module: SizingModule, anzahl:int, ausrichtung?:string, neigung?:float, label?:string}>  $daecher
     * @param  array<string, mixed>  $opt
     * @return array<string, mixed>
     */
    public function auswerten(array $daecher, SizingInverter $wr, ?SizingBattery $bat = null, array $opt = []): array
    {
        $regelnString = [];   // gemischt je Dach/String, danach worst-by-name
        $daecherOut = [];
        $totalPdc = 0.0;
        $totalModule = 0;
        $totalStrings = 0;

        foreach ($daecher as $dach) {
            $modul = $dach['module'];
            $anzahl = (int) ($dach['anzahl'] ?? 0);
            if ($anzahl <= 0) {
                continue;
            }
            $roofs = [['module_count' => $anzahl, 'ausrichtung' => $dach['ausrichtung'] ?? '', 'neigung' => $dach['neigung'] ?? null]];
            $cfg = $this->builder->build($modul, $wr, $roofs, $opt);

            $dachRegeln = [];
            foreach ($cfg['mppts'] as $mppt) {
                $strings = $mppt['strings'] ?? [];
                $parallel = max(1, count($strings));
                $totalStrings += count($strings);
                foreach ($strings as $s) {
                    foreach ($this->engine->pruefeString($modul, $wr, (int) $s['module_count'], $parallel, $opt)['regeln'] as $r) {
                        $dachRegeln[] = $r;
                    }
                }
            }

            $pmpp = (int) $modul->pmpp_wp;
            $totalPdc += $anzahl * $pmpp;
            $totalModule += $anzahl;

            $daecherOut[] = [
                'label' => $dach['label'] ?? 'Modul',
                'ausrichtung' => $dach['ausrichtung'] ?? '',
                'neigung' => $dach['neigung'] ?? null,
                'anzahl' => $anzahl,
                'pmpp_wp' => $pmpp,
                'kwp' => round($anzahl * $pmpp / 1000, 2),
                'strings' => count($cfg['mppts']) ? array_sum(array_map(fn ($m) => count($m['strings'] ?? []), $cfg['mppts'])) : 0,
                'status' => $this->engine->gesamtAmpel($dachRegeln),
                'warnungen' => $cfg['warnungen'],
            ];

            $regelnString = $this->mergeWorst($regelnString, $dachRegeln);
        }

        // Globale Regeln (einmal je WR)
        $global = [$this->engine->ueberdimensionierungAusLeistung($totalPdc, $wr)];
        $global[] = $this->mpptKapazitaet($wr, $totalStrings);
        foreach ($this->engine->netzregeln($wr, $opt) as $r) {
            $global[] = $r;
        }
        $global[] = $this->engine->temperaturBereich($wr, $opt);
        if ($bat !== null) {
            $global[] = $this->engine->batterieKompatibilitaet($wr, $bat, $opt);
        }

        $alle = array_merge(array_values($regelnString), $global);
        $gesamt = $this->engine->gesamtAmpel($alle);

        return [
            'gesamt_status' => $gesamt,
            'freigabe' => $gesamt !== 'rot',
            'alarm' => $gesamt === 'rot',
            'total_kwp' => round($totalPdc / 1000, 2),
            'total_module' => $totalModule,
            'total_strings' => $totalStrings,
            'p_dc_w' => (int) $totalPdc,
            'dc_ac_ratio' => $wr->p_ac_nenn_w > 0 ? round($totalPdc / $wr->p_ac_nenn_w, 3) : null,
            'daecher' => $daecherOut,
            'rules' => $this->engine->formatRegeln($alle),
        ];
    }

    /**
     * @param  array<string, array<string, mixed>>  $acc
     * @param  array<int, array<string, mixed>>  $neu
     * @return array<string, array<string, mixed>>
     */
    private function mergeWorst(array $acc, array $neu): array
    {
        foreach ($neu as $r) {
            $key = $r['name'];
            if (! isset($acc[$key]) || $this->engine->ampelRang($r['status']) > $this->engine->ampelRang($acc[$key]['status'])) {
                $acc[$key] = $r;
            }
        }

        return $acc;
    }

    private function mpptKapazitaet(SizingInverter $wr, int $totalStrings): array
    {
        $kapazitaet = 0;
        for ($i = 0; $i < (int) $wr->anzahl_mppt; $i++) {
            $kapazitaet += $wr->stringsProMppt($i);
        }
        $status = $totalStrings > $kapazitaet ? 'rot' : ($totalStrings > 0 ? 'gruen' : 'gelb');

        return [
            'nr' => 12, 'name' => 'String-/MPPT-Kapazität', 'norm' => 'WR-Datenblatt', 'status' => $status,
            'text' => "{$totalStrings} Strings ≤ {$kapazitaet} Eingänge ({$wr->anzahl_mppt} MPPT)", 'grenze' => $kapazitaet,
        ];
    }
}
