<?php

namespace Tests\Unit\Heizlast;

use App\Services\Heizlast\HeizlastRechner;
use App\Services\Heizlast\RaumHuelleService;
use Tests\TestCase;

/**
 * G0a / AP-4a — Referenztest für P5 (Raumvolumen = Fläche × Höhe), test/spec-only.
 * Belegt über den Lüftungswärmeverlust H_V = 0,34 · n · V (V = Grundfläche · Höhe), DIN EN 12831-1.
 */
class HeizlastRechnerReferenzTest extends TestCase
{
    private function rechner(): HeizlastRechner
    {
        return new HeizlastRechner(new RaumHuelleService);
    }

    private function eingabe(float $flaeche, float $hoehe, float $n): array
    {
        return [
            'norm_aussentemp_c' => -12.0,
            'raeume' => [[
                'name' => 'R', 'nutzung' => 'wohnen',
                'grundflaeche_m2' => $flaeche, 'hoehe_m' => $hoehe, 'luftwechsel_1h' => $n,
                'bauteile' => [],
            ]],
        ];
    }

    /** P5 — V = 25 m² × 2,5 m = 62,5 m³ → H_V = 0,34 · 0,5 · 62,5 = 10,625 W/K. */
    public function test_p5_raumvolumen_ueber_h_v(): void
    {
        $res = $this->rechner()->berechne($this->eingabe(25.0, 2.5, 0.5));
        $hV = (float) $res['raeume'][0]['h_v_w_k'];

        $this->assertEqualsWithDelta(10.625, $hV, 0.02); // 0,34·0,5·62,5
    }

    /** P5-Ergänzung — Volumen ist linear in der Höhe (doppelte Höhe → doppeltes V → doppeltes H_V). */
    public function test_p5_volumen_linear_in_hoehe(): void
    {
        $a = (float) $this->rechner()->berechne($this->eingabe(25.0, 2.5, 0.5))['raeume'][0]['h_v_w_k'];
        $b = (float) $this->rechner()->berechne($this->eingabe(25.0, 5.0, 0.5))['raeume'][0]['h_v_w_k'];

        $this->assertEqualsWithDelta(2.0, $b / $a, 0.001);
    }
}
