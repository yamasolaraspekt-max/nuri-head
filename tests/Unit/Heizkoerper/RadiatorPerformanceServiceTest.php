<?php

namespace Tests\Unit\Heizkoerper;

use App\Services\Heizkoerper\RadiatorPerformanceService;
use Tests\TestCase;

/**
 * Paritäts-Test (M3 iv-a): 1:1 aus wberechnung tests/Unit/Heizlast/HeizkoerperServiceTest.php @ d81faa8.
 * Erwartungswerte UNVERÄNDERT — beweist byte-genauen Port von HeizkoerperService -> RadiatorPerformanceService.
 * DB-frei (reiner Kern).
 */
class RadiatorPerformanceServiceTest extends TestCase
{
    private function hk(float $qNorm = 2000): array
    {
        return [['q_norm_w' => $qNorm, 'exponent_n' => 1.30, 'norm_bedingung' => '75/65/20']];
    }

    public function test_normpunkt_liefert_normleistung(): void
    {
        $s = new RadiatorPerformanceService;
        // 75/65/20 mit Spreizung 10 → Δt_log = Δt_norm → Q_real = Q_norm
        $this->assertEqualsWithDelta(2000, $s->qReal($this->hk(), 75, 20, 10), 5);
    }

    public function test_q_real_faellt_bei_niedrigem_vorlauf(): void
    {
        $s = new RadiatorPerformanceService;
        // 2000-W-Heizkörper bei 45 °C VL / 7 K Spreizung → ≈ 660 W (≈ 33 % der Normleistung)
        $this->assertEqualsWithDelta(663, $s->qReal($this->hk(), 45, 20, 7), 20);
    }

    public function test_leistungstabelle_sinkt_mit_vorlauftemperatur(): void
    {
        $s = new RadiatorPerformanceService;
        $t = $s->leistungstabelle($this->hk(2000), 20);

        $this->assertCount(5, $t);
        $this->assertSame(75, $t[0]['vorlauf']);
        $this->assertEqualsWithDelta(2000, $t[0]['q_w'], 30);      // 75/65/20 = Normbedingung
        $this->assertGreaterThan($t[1]['q_w'], $t[0]['q_w']);      // monoton fallend
        $this->assertGreaterThan($t[4]['q_w'], $t[1]['q_w']);
        $this->assertSame(35, $t[4]['vorlauf']);
    }

    public function test_status_ampel(): void
    {
        $s = new RadiatorPerformanceService;
        $this->assertSame('gruen', $s->status(700, 663));
        $this->assertSame('gelb', $s->status(663, 700));   // 94,7 %
        $this->assertSame('rot', $s->status(663, 800));     // 82,9 %
    }

    public function test_min_vorlauftemperatur(): void
    {
        $s = new RadiatorPerformanceService;
        // Heizlast = Q_real(45 °C) → Mindest-Vorlauf ≈ 45 °C
        $q45 = $s->qReal($this->hk(), 45, 20, 7);
        $this->assertEqualsWithDelta(45, $s->minVorlauf($this->hk(), $q45, 20, 7), 1.0);
        // Höhere Heizlast → höhere Mindest-Vorlauftemperatur
        $this->assertGreaterThan(45, $s->minVorlauf($this->hk(), $q45 * 2, 20, 7));
        // Unzureichende Heizfläche → null (auch bei 90 °C nicht ausreichend)
        $this->assertNull($s->minVorlauf($this->hk(200), 5000, 20, 7));
    }
}
