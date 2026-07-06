<?php

namespace Tests\Unit\Form;

use App\Services\Form\UnitConversionService;
use PHPUnit\Framework\TestCase;

/**
 * FS-03 — Einheiten-Umrechnung (framework-neutral, DB-frei).
 */
class UnitConversionServiceTest extends TestCase
{
    private function svc(): UnitConversionService
    {
        return new UnitConversionService();
    }

    public function test_laenge_umrechnung(): void
    {
        $s = $this->svc();
        $this->assertEqualsWithDelta(1.0, $s->convert(100.0, 'cm', 'm'), 1e-9);
        $this->assertEqualsWithDelta(1000.0, $s->convert(1.0, 'm', 'mm'), 1e-9);
        $this->assertEqualsWithDelta(2.0, $s->convert(2000.0, 'm', 'km'), 1e-9);
    }

    public function test_gleiche_einheit_unveraendert(): void
    {
        $this->assertSame(5.0, $this->svc()->convert(5.0, 'm', 'm'));
    }

    public function test_einheitenlos_unveraendert(): void
    {
        $this->assertSame(7.0, $this->svc()->convert(7.0, null, null));
    }

    public function test_dimensionsfremd_liefert_null(): void
    {
        $this->assertNull($this->svc()->convert(5.0, 'kg', 'm'));
        // eine Seite einheitenlos, andere mit Maß → nicht eindeutig → null
        $this->assertNull($this->svc()->convert(5.0, null, 'm'));
    }

    public function test_dimension_und_kompatibilitaet(): void
    {
        $s = $this->svc();
        $this->assertSame('laenge', $s->dimension('CM'));
        $this->assertSame('masse', $s->dimension('kg'));
        $this->assertNull($s->dimension('unbekannt'));
        $this->assertTrue($s->sindKompatibel('cm', 'm'));
        $this->assertFalse($s->sindKompatibel('cm', 'kg'));
        $this->assertTrue($s->kennt('m²'));
        $this->assertFalse($s->kennt('parsec'));
    }

    public function test_normalize_trim_und_kleinschreibung(): void
    {
        $s = $this->svc();
        $this->assertSame('cm', $s->normalize('  CM '));
        $this->assertNull($s->normalize('   '));
        $this->assertNull($s->normalize(null));
    }
}
