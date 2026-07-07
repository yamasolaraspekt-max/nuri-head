<?php

namespace Tests\Unit\Heizlast;

use App\Services\Heizlast\HoehenkorrekturService;
use PHPUnit\Framework\TestCase;

class HoehenkorrekturServiceTest extends TestCase
{
    private HoehenkorrekturService $svc;

    protected function setUp(): void
    {
        parent::setUp();
        $this->svc = new HoehenkorrekturService;
    }

    public function test_keine_korrektur_unter_200_m_differenz(): void
    {
        // 150 m über Bezugshöhe → unter Schwelle, θe unverändert.
        $r = $this->svc->korrigiere(-12.0, 250.0, 100.0);

        $this->assertFalse($r['korrigiert']);
        $this->assertSame(-12.0, $r['norm_aussentemp_c']);
        $this->assertSame(0.0, $r['delta_theta_e_k']);
    }

    public function test_hoeher_gelegenes_gebaeude_wird_kaelter(): void
    {
        // 300 m höher als Bezugshöhe → Δθe = -0,01 · 300 = -3,0 K (kälter).
        $r = $this->svc->korrigiere(-12.0, 400.0, 100.0);

        $this->assertTrue($r['korrigiert']);
        $this->assertSame(-3.0, $r['delta_theta_e_k']);
        $this->assertSame(-15.0, $r['norm_aussentemp_c']);
        $this->assertSame(300.0, $r['hoehendifferenz_m']);
    }

    public function test_tiefer_gelegenes_gebaeude_wird_waermer(): void
    {
        // 250 m tiefer als Bezugshöhe → Δθe = -0,01 · (-250) = +2,5 K (wärmer).
        $r = $this->svc->korrigiere(-14.0, 50.0, 300.0);

        $this->assertTrue($r['korrigiert']);
        $this->assertSame(2.5, $r['delta_theta_e_k']);
        $this->assertSame(-11.5, $r['norm_aussentemp_c']);
    }

    public function test_genau_200_m_loest_korrektur_aus(): void
    {
        // Schwelle ist erreicht (≥ 200 m) → Δθe = -2,0 K.
        $r = $this->svc->korrigiere(-10.0, 300.0, 100.0);

        $this->assertTrue($r['korrigiert']);
        $this->assertSame(-2.0, $r['delta_theta_e_k']);
        $this->assertSame(-12.0, $r['norm_aussentemp_c']);
    }

    public function test_ohne_standorthoehe_keine_korrektur(): void
    {
        $r = $this->svc->korrigiere(-12.0, null, 100.0);

        $this->assertFalse($r['korrigiert']);
        $this->assertSame(-12.0, $r['norm_aussentemp_c']);
        $this->assertNull($r['hoehendifferenz_m']);
    }

    public function test_ohne_bezugshoehe_keine_korrektur(): void
    {
        $r = $this->svc->korrigiere(-12.0, 800.0, null);

        $this->assertFalse($r['korrigiert']);
        $this->assertSame(-12.0, $r['norm_aussentemp_c']);
    }

    public function test_ergebnis_wird_auf_eine_nachkommastelle_gerundet(): void
    {
        // 230 m → Δθe = -2,3 K; Basis -8,5 → -10,8 °C, sauber gerundet.
        $r = $this->svc->korrigiere(-8.5, 343.0, 113.0);

        $this->assertSame(-2.3, $r['delta_theta_e_k']);
        $this->assertSame(-10.8, $r['norm_aussentemp_c']);
    }
}
