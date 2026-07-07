<?php

namespace Tests\Unit\Heizlast;

use App\Services\Heizlast\FoerderungService;
use PHPUnit\Framework\TestCase;

class FoerderungServiceTest extends TestCase
{
    private FoerderungService $svc;

    protected function setUp(): void
    {
        parent::setUp();
        $this->svc = new FoerderungService;
    }

    /** @param array<string,mixed> $o */
    private function in(array $o = []): array
    {
        return array_merge([
            'foerderfaehige_kosten' => 30000, 'rabatt' => 0, 'zusatz' => 0,
            'anzahl_we' => 1, 'selbst_bewohnte_we' => 1, 'we_unter_40k' => 0,
            'heizungsart' => 'oel', 'heizung_alter' => 25, 'effizienzbonus' => true,
        ], $o);
    }

    public function test_self_occupier_old_oil_efficient_30plus5plus20(): void
    {
        $r = $this->svc->berechne($this->in());
        // 30 % Grund + 5 % Effizienz + 20 % Klima = 55 %
        $this->assertSame(55, $r['saetze']['selbst_ohne_pct']);
        $this->assertEqualsWithDelta(16500, $r['zuschuss'], 0.01);
        $this->assertEqualsWithDelta(13500, $r['netto_investition'], 0.01);
        $this->assertEqualsWithDelta(55.0, $r['effektiver_satz_pct'], 0.01);
        $this->assertTrue($r['klima_berechtigt']);
    }

    public function test_income_bonus_capped_at_70(): void
    {
        // 30 + 5 + 20 + 30 = 85 % → Deckel 70 %
        $r = $this->svc->berechne($this->in(['we_unter_40k' => 1]));
        $this->assertSame(70, $r['saetze']['selbst_mit_pct']);
        $this->assertEqualsWithDelta(21000, $r['zuschuss'], 0.01);
        $this->assertEqualsWithDelta(9000, $r['netto_investition'], 0.01);
        $this->assertEqualsWithDelta(70.0, $r['effektiver_satz_pct'], 0.01);
    }

    public function test_costs_capped_at_eligible_ceiling(): void
    {
        $r = $this->svc->berechne($this->in(['foerderfaehige_kosten' => 40000]));
        $this->assertEqualsWithDelta(30000, $r['foerderfaehig'], 0.01);
        $this->assertEqualsWithDelta(16500, $r['zuschuss'], 0.01);   // 55 % auf 30.000 €
        $this->assertEqualsWithDelta(23500, $r['netto_investition'], 0.01); // 40.000 − 16.500
    }

    public function test_functioning_gas_under_20y_has_no_climate_bonus(): void
    {
        $r = $this->svc->berechne($this->in(['heizungsart' => 'gas', 'heizung_alter' => 10]));
        $this->assertFalse($r['klima_berechtigt']);
        // nur 30 + 5 = 35 %
        $this->assertEqualsWithDelta(10500, $r['zuschuss'], 0.01);
    }

    public function test_rented_unit_gets_no_climate_or_income_bonus(): void
    {
        $r = $this->svc->berechne($this->in(['selbst_bewohnte_we' => 0]));
        $this->assertSame(35, $r['saetze']['vermietet_pct']);
        $this->assertEqualsWithDelta(10500, $r['zuschuss'], 0.01); // 35 % trotz altem Öl
    }

    public function test_eligible_cost_ceiling_scales_with_units(): void
    {
        $this->assertSame(30000, $this->svc->deckel(1));
        $this->assertSame(60000, $this->svc->deckel(3));   // 30k + 2×15k
        $this->assertSame(113000, $this->svc->deckel(7));  // 30k + 5×15k + 1×8k
    }

    public function test_envelope_measure_base_15_percent(): void
    {
        $r = $this->svc->huelle(['huelle_kosten' => 20000, 'isfp' => false, 'anzahl_we' => 1]);
        $this->assertSame(15, $r['satz_pct']);
        $this->assertEqualsWithDelta(3000, $r['zuschuss'], 0.01);    // 15 % von 20.000
        $this->assertEqualsWithDelta(17000, $r['netto_investition'], 0.01);
    }

    public function test_envelope_measure_isfp_adds_5_percent_and_raises_ceiling(): void
    {
        $r = $this->svc->huelle(['huelle_kosten' => 50000, 'isfp' => true, 'anzahl_we' => 1]);
        $this->assertSame(20, $r['satz_pct']);                       // 15 % + iSFP 5 %
        $this->assertSame(60000, $r['deckel']);                      // iSFP hebt Deckel auf 60k
        $this->assertEqualsWithDelta(10000, $r['zuschuss'], 0.01);   // 20 % von 50.000
    }
}
