<?php

declare(strict_types=1);

namespace Tests\Unit\Energie;

use App\Services\Energie\Dto\BatterySpec;
use App\Services\Energie\Dto\InverterSpec;
use App\Services\Energie\Dto\ModuleSpec;
use App\Services\Energie\InverterSizingService;
use PHPUnit\Framework\TestCase;

/**
 * Detaillierte Engine (Playground-Niveau): Spannungsfenster, zweistufige Sicherung,
 * Netzregeln (VDE-AR-N 4105/4110), WR-Temperatur, Batterie.
 *
 * Paritäts-Beweis der Portierung aus wberechnung: Die Engine-Formeln und die erwarteten
 * Ergebnisse (Assertions) sind unverändert; nur die Fixture-Konstruktion nutzt statt der
 * wb-Eloquent-Factories die ticket-Adapter-DTOs (ModuleSpec/InverterSpec/BatterySpec::fromRow),
 * gefüttert mit denselben Datenblatt-Werten wie die wb-Factories. Die Modul-Werte tragen die
 * gleichen Schlüssel wie in wb; die Inverter-Werte werden auf die ticket-`inverters`-Spaltennamen
 * gemappt (z. B. u_dc_max_v → max_input_voltage), die fromRow erwartet.
 */
final class InverterSizingServiceTest extends TestCase
{
    private InverterSizingService $svc;

    protected function setUp(): void
    {
        parent::setUp();
        $this->svc = new InverterSizingService;
    }

    /** Referenz-TOPCon-Modul (wb PvModuleFactory-Defaults, gleiche Schlüssel wie ModuleSpec). */
    private function modul(array $o = []): ModuleSpec
    {
        return ModuleSpec::fromRow(array_merge([
            'pmpp_wp' => 415,
            'voc_v' => 38.5,
            'isc_a' => 13.8,
            'vmpp_v' => 32.0,
            'impp_a' => 13.0,
            'tk_voc_pct_k' => -0.250,
            'tk_isc_pct_k' => 0.045,
            'tk_pmpp_pct_k' => -0.300,
            'tk_vmpp_pct_k' => null,
            'u_sys_max_v' => 1000,
            'sicherung_max_a' => 25,
        ], $o));
    }

    /** Referenz-Hybrid-WR (wb InverterFactory-Defaults, gemappt auf ticket-inverters-Spalten). */
    private function wr(array $o = []): InverterSpec
    {
        return InverterSpec::fromRow(array_merge([
            'max_input_voltage' => 1000,
            'dc_startup_voltage' => 180,
            'min_mpp_voltage' => 160,
            'max_mpp_voltage' => 950,
            'num_mpp_trackers' => 2,
            'max_input_current_per_mpp' => 26,
            'max_input_current' => 16,
            'max_short_circuit_current_per_mpp' => 40,
            'ac_nominal_power' => 10000,
            'max_ac_power' => 10000,
            'max_dc_power' => 15000,
            'max_dc_ac_ratio' => 1.50,
            'num_phases' => '3',
            'is_hybrid' => true,
            'integrated_grid_protection' => true,
            'vde4105_compliant' => true,
            'controllable_14a' => true,
            'eps_capable' => true,
            'active_power_limit' => 'dynamisch',
            'control_interface' => 'EEBus/Modbus',
            'operating_temp_min_c' => -25,
            'operating_temp_max_c' => 60,
            'temp_derating_from_c' => 45,
            'battery_min_voltage' => 80,
            'battery_max_voltage' => 600,
            'battery_max_charge_power_w' => 5000,
            'battery_max_current_a' => 25,
        ], $o));
    }

    /** Referenz-LFP-Batterie (wb BatteryFactory-Defaults, gemappt auf ticket-batteries-Spalten). */
    private function bat(array $o = []): BatterySpec
    {
        return BatterySpec::fromRow(array_merge([
            'min_voltage' => 200,
            'max_voltage' => 451,
            'max_charge_power_kw' => 5.0,
            'max_current_a' => 25,
        ], $o));
    }

    public function test_effective_tk_vmpp_is_conservative_fallback(): void
    {
        // γ − α = −0,30 − 0,045 = −0,345 %/K
        $this->assertEqualsWithDelta(-0.345, $this->svc->effektiverTkVmpp($this->modul(['tk_vmpp_pct_k' => null])), 0.001);
        $this->assertEqualsWithDelta(-0.31, $this->svc->effektiverTkVmpp($this->modul(['tk_vmpp_pct_k' => -0.31])), 0.001);
    }

    public function test_spannungsfenster_reference(): void
    {
        $f = $this->svc->spannungsfenster($this->modul(['tk_vmpp_pct_k' => -0.25]), $this->wr(), ['tmin_c' => -10, 'tmax_cell_c' => 70]);
        $this->assertEqualsWithDelta(41.87, $f['voc_tmin_v'], 0.05);
        $this->assertSame(23, $f['n_max']);   // floor(1000 / 41,87)
        $this->assertSame(7, $f['n_min']);    // Startspannung 180 V bindet
        $this->assertTrue($f['gueltig']);
    }

    public function test_two_stage_string_fuse(): void
    {
        $m = $this->modul(); // Isc 13,8 A, sicherung_max 25 A
        // < 3 parallel → keine Sicherung nötig
        $this->assertSame('gruen', $this->svc->strangsicherung($m, 2)['status']);
        // 3 parallel: In ≥ 1,5625 × 13,8 ≈ 21,6 A ≤ 25 A → grün
        $this->assertSame('gruen', $this->svc->strangsicherung($m, 3)['status']);
        // Modul mit kleiner Maximalsicherung → rot
        $this->assertSame('rot', $this->svc->strangsicherung($this->modul(['sicherung_max_a' => 20]), 3)['status']);
    }

    public function test_netzregeln_single_phase_schieflast(): void
    {
        $rot = collect($this->svc->netzregeln($this->wr(['num_phases' => '1', 'max_ac_power' => 5000])))
            ->firstWhere('name', 'Schieflast (1-phasig)');
        $this->assertSame('rot', $rot['status']); // 5000 VA > 4600 VA → 3-phasig zwingend

        $dreiphasig = collect($this->svc->netzregeln($this->wr(['num_phases' => '3'])))->pluck('status')->all();
        $this->assertNotContains('rot', $dreiphasig);
    }

    public function test_netzregeln_flag_na_schutz_and_14a(): void
    {
        $regeln = collect($this->svc->netzregeln($this->wr(['integrated_grid_protection' => false])));
        $this->assertSame('rot', $regeln->firstWhere('name', 'NA-Schutz')['status']);

        $r14a = collect($this->svc->netzregeln($this->wr(['active_power_limit' => 'keine', 'controllable_14a' => false])))
            ->firstWhere('name', 'Einspeisemanagement / §14a');
        $this->assertSame('rot', $r14a['status']);
    }

    public function test_temperatur_bereich(): void
    {
        $wr = $this->wr(); // -25…60 °C, Derating ab 45
        $this->assertSame('gruen', $this->svc->temperaturBereich($wr, ['tamb_max_c' => 40])['status']);
        $this->assertSame('gelb', $this->svc->temperaturBereich($wr, ['tamb_max_c' => 50])['status']); // Derating
        $this->assertSame('rot', $this->svc->temperaturBereich($wr, ['tamb_max_c' => 65])['status']);   // außerhalb
    }

    public function test_batterie_kompatibilitaet(): void
    {
        $wr = $this->wr(); // hybrid, u_bat 80…600
        $ok = $this->bat(['min_voltage' => 200, 'max_voltage' => 451]);
        $this->assertSame('gruen', $this->svc->batterieKompatibilitaet($wr, $ok)['status']);

        $zuHoch = $this->bat(['min_voltage' => 200, 'max_voltage' => 700]);
        $this->assertSame('rot', $this->svc->batterieKompatibilitaet($wr, $zuHoch)['status']);

        $this->assertSame('rot', $this->svc->batterieKompatibilitaet($this->wr(['is_hybrid' => false]), $ok)['status']);
    }

    public function test_evaluate_konfiguration_returns_ui_shape_with_rich_rules(): void
    {
        $result = $this->svc->evaluateKonfiguration(
            $this->modul(['tk_vmpp_pct_k' => -0.25]),
            $this->wr(),
            ['mppts' => [['strings' => [['module_count' => 22, 'ausrichtung' => 'Süd', 'neigung' => 35]]]]],
        );

        $this->assertSame('gruen', $result['gesamt_status']);
        $this->assertSame(7, $result['n_min']);
        $this->assertSame(23, $result['n_max']);
        // UI-Form: jede Regel hat id/titel/norm/status/wert_text
        $this->assertArrayHasKey('titel', $result['rules'][0]);
        $this->assertArrayHasKey('wert_text', $result['rules'][0]);
        // Reichhaltige Regeln vorhanden: Netz (4105) + DC-Überdimensionierung
        $this->assertTrue(collect($result['rules'])->contains(fn ($r) => str_contains($r['norm'], 'VDE-AR-N 4105')));
        $this->assertTrue(collect($result['rules'])->contains(fn ($r) => $r['titel'] === 'DC-Überdimensionierung'));
    }
}
