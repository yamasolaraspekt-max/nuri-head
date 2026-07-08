<?php

namespace Tests\Unit\Form;

use App\Services\Form\FormSchemaValidator;
use PHPUnit\Framework\TestCase;

/**
 * FS-02 — Server-Validierung des v2-`fields`-Schemas. Reine Funktion (kein DB/Framework).
 */
class FormSchemaValidatorTest extends TestCase
{
    private FormSchemaValidator $v;

    protected function setUp(): void
    {
        $this->v = new FormSchemaValidator;
    }

    private function gueltigeFelder(): array
    {
        return [
            ['key' => 'bauart', 'label' => 'Bauart', 'type' => 'select', 'required' => true,
                'options' => [['value' => 'neubau', 'label' => 'Neubau'], ['value' => 'bestand', 'label' => 'Bestand']]],
            ['key' => 'personen', 'label' => 'Personen', 'type' => 'integer', 'min' => 1, 'max' => 20],
            ['key' => 'laenge', 'label' => 'Länge', 'type' => 'length', 'unit' => 'm', 'decimals' => 2],
            ['key' => 'breite', 'label' => 'Breite', 'type' => 'length', 'unit' => 'm', 'decimals' => 2],
            ['key' => 'flaeche', 'label' => 'Fläche', 'type' => 'calculation', 'unit' => 'm2', 'decimals' => 2,
                'calculation' => ['formel' => 'FLAECHE(laenge,breite)', 'rundung' => 'kaufmaennisch']],
            ['key' => 'sanierung_art', 'label' => 'Sanierung', 'type' => 'select',
                'options' => [['value' => 'ja', 'label' => 'Ja']],
                'visible_if' => ['field' => 'bauart', 'op' => '=', 'value' => 'bestand']],
        ];
    }

    public function test_gueltiges_v2_schema_ist_fehlerfrei(): void
    {
        $this->assertSame([], $this->v->validate($this->gueltigeFelder()));
    }

    public function test_fehlender_und_ungueltiger_key(): void
    {
        $f = $this->v->validate([
            ['label' => 'Ohne Key', 'type' => 'text'],
            ['key' => 'Groß Falsch', 'label' => 'Bad Slug', 'type' => 'text'],
        ]);
        $this->assertTrue($this->hat($f, "'key' fehlt"));
        $this->assertTrue($this->hat($f, 'kein gültiger Slug'));
    }

    public function test_doppelter_key_wird_erkannt(): void
    {
        $f = $this->v->validate([
            ['key' => 'x', 'label' => 'A', 'type' => 'text'],
            ['key' => 'x', 'label' => 'B', 'type' => 'text'],
        ]);
        $this->assertTrue($this->hat($f, 'doppelt'));
    }

    public function test_unbekannter_typ(): void
    {
        $f = $this->v->validate([['key' => 'a', 'label' => 'A', 'type' => 'zauberfeld']]);
        $this->assertTrue($this->hat($f, 'nicht in Whitelist'));
    }

    public function test_auswahltyp_ohne_optionen_und_optionen_bei_nichtauswahl(): void
    {
        $f1 = $this->v->validate([['key' => 'a', 'label' => 'A', 'type' => 'select']]);
        $this->assertTrue($this->hat($f1, "braucht nicht-leere 'options'"));

        $f2 = $this->v->validate([['key' => 'a', 'label' => 'A', 'type' => 'text', 'options' => [['value' => 1, 'label' => 'x']]]]);
        $this->assertTrue($this->hat($f2, "'options' nur bei Auswahltypen"));
    }

    public function test_min_groesser_max(): void
    {
        $f = $this->v->validate([['key' => 'a', 'label' => 'A', 'type' => 'number', 'min' => 10, 'max' => 5]]);
        $this->assertTrue($this->hat($f, "'min' > 'max'"));
    }

    public function test_visible_if_shape_und_referenz(): void
    {
        $f1 = $this->v->validate([['key' => 'a', 'label' => 'A', 'type' => 'text', 'visible_if' => ['field' => 'b', 'value' => 1]]]);
        $this->assertTrue($this->hat($f1, 'visible_if.op'));
        $this->assertTrue($this->hat($f1, "unbekanntes Feld 'b'"));

        $f2 = $this->v->validate([
            ['key' => 'b', 'label' => 'B', 'type' => 'text'],
            ['key' => 'a', 'label' => 'A', 'type' => 'text', 'visible_if' => ['field' => 'b', 'op' => '=', 'value' => 'x']],
        ]);
        $this->assertSame([], $f2);
    }

    public function test_calculation_nur_bei_calculation_typ_und_formel_pflicht(): void
    {
        $f1 = $this->v->validate([['key' => 'a', 'label' => 'A', 'type' => 'calculation']]);
        $this->assertTrue($this->hat($f1, 'braucht calculation.formel'));

        $f2 = $this->v->validate([['key' => 'a', 'label' => 'A', 'type' => 'text', 'calculation' => ['formel' => 'x']]]);
        $this->assertTrue($this->hat($f2, "'calculation' nur bei type=calculation"));
    }

    public function test_ungueltige_enums(): void
    {
        $f = $this->v->validate([['key' => 'a', 'label' => 'A', 'type' => 'text', 'source' => 'magie', 'risk_level' => 'hoch']]);
        $this->assertTrue($this->hat($f, "'source' 'magie'"));
        $this->assertTrue($this->hat($f, "'risk_level' 'hoch'"));
    }

    public function test_validateFormula_prueft_kopfversion(): void
    {
        $this->assertNotEmpty($this->v->validateFormula(1, $this->gueltigeFelder()));
        $this->assertSame([], $this->v->validateFormula(2, $this->gueltigeFelder()));
    }

    public function test_leeres_oder_nichtarray_fields(): void
    {
        $this->assertNotEmpty($this->v->validate([]));
        $this->assertNotEmpty($this->v->validate('kein array'));
    }

    private function hat(array $fehler, string $teil): bool
    {
        foreach ($fehler as $f) {
            if (str_contains($f, $teil)) {
                return true;
            }
        }

        return false;
    }
}
