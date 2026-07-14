<?php

namespace Tests\Unit\Heizlast;

use App\Services\Heizlast\RaumHuelleService;
use Tests\TestCase;

/**
 * G0a / AP-4a — Referenztest des VORHANDENEN Öffnungsabzugs (test/spec-only).
 * Belegt P3 der Startblock-Matrix + dokumentiert die Öffnung>Wand-Lücke (G0b).
 */
class RaumHuelleServiceTest extends TestCase
{
    private function service(): RaumHuelleService
    {
        return new RaumHuelleService;
    }

    /** P3 — Netto-Wandfläche 30 − 6 = 24,00 m² (DIN EN 12831-1 §6). */
    public function test_p3_wandoeffnung_30_minus_6_ist_24_qm(): void
    {
        $bauteile = [
            ['typ' => 'wand', 'grenzflaeche' => 'aussen', 'flaeche_m2' => 30],
            ['typ' => 'fenster', 'grenzflaeche' => 'aussen', 'flaeche_m2' => 6],
        ];
        $eff = $this->service()->effektiveBauteile($bauteile);
        $wand = collect($eff)->firstWhere('typ', 'wand');

        $this->assertSame(24.0, (float) $wand['flaeche_eff']);
    }

    /** P3-Ergänzung — zwei Fenster additiv: 30 − (6+4) = 20,00 m². */
    public function test_p3_zwei_fenster_additiv_ist_20_qm(): void
    {
        $bauteile = [
            ['typ' => 'wand', 'grenzflaeche' => 'aussen', 'flaeche_m2' => 30],
            ['typ' => 'fenster', 'grenzflaeche' => 'aussen', 'flaeche_m2' => 6],
            ['typ' => 'fenster', 'grenzflaeche' => 'aussen', 'flaeche_m2' => 4],
        ];
        $eff = $this->service()->effektiveBauteile($bauteile);
        $wand = collect($eff)->firstWhere('typ', 'wand');

        $this->assertSame(20.0, (float) $wand['flaeche_eff']);
    }

    /**
     * LÜCKE (G0b/AP-4b) — Öffnung > Wand wird heute STILL auf 0 geklemmt, ohne Blocker-Markierung.
     * Wird nur dokumentiert, NICHT durch Produktivcode grün gemacht.
     */
    public function test_oeffnung_groesser_wand_wird_still_geklemmt_luecke(): void
    {
        $bauteile = [
            ['typ' => 'wand', 'grenzflaeche' => 'aussen', 'flaeche_m2' => 30],
            ['typ' => 'fenster', 'grenzflaeche' => 'aussen', 'flaeche_m2' => 40],
        ];
        $eff = $this->service()->effektiveBauteile($bauteile);
        $wand = collect($eff)->firstWhere('typ', 'wand');

        // Heutiges Ist: max(0, (30-40)/30) = 0 → Wand-Netto 0, KEINE Blocker-Markierung im Ergebnis.
        $this->assertSame(0.0, (float) $wand['flaeche_eff']);
        $this->markTestIncomplete('Öffnung > Wand wird still auf 0 geklemmt ohne Blocker-Markierung — Absicherung fehlt (G0b/AP-4b).');
    }
}
