<?php

namespace Tests\Unit\Form;

use App\Services\Form\VisibleIfService;
use PHPUnit\Framework\TestCase;

/**
 * FS-04 — Bedingte Sichtbarkeit. Server-Auswertung, Kaskade und die Kernregel
 * „unsichtbares Pflichtfeld blockt nicht".
 */
class VisibleIfServiceTest extends TestCase
{
    private VisibleIfService $s;

    protected function setUp(): void
    {
        $this->s = new VisibleIfService;
    }

    public function test_null_bedingung_ist_immer_sichtbar(): void
    {
        $this->assertTrue($this->s->istSichtbar(null, []));
    }

    public function test_vergleichsoperatoren(): void
    {
        $a = ['bauart' => 'bestand', 'personen' => 4];
        $this->assertTrue($this->s->istSichtbar(['field' => 'bauart', 'op' => '=', 'value' => 'bestand'], $a));
        $this->assertFalse($this->s->istSichtbar(['field' => 'bauart', 'op' => '=', 'value' => 'neubau'], $a));
        $this->assertTrue($this->s->istSichtbar(['field' => 'bauart', 'op' => '!=', 'value' => 'neubau'], $a));
        $this->assertTrue($this->s->istSichtbar(['field' => 'personen', 'op' => '>', 'value' => 3], $a));
        $this->assertFalse($this->s->istSichtbar(['field' => 'personen', 'op' => '<', 'value' => 3], $a));
        $this->assertTrue($this->s->istSichtbar(['field' => 'personen', 'op' => '>=', 'value' => 4], $a));
        $this->assertTrue($this->s->istSichtbar(['field' => 'personen', 'op' => '<=', 'value' => 4], $a));
    }

    public function test_in_und_not_in(): void
    {
        $a = ['medium' => 'gas'];
        $this->assertTrue($this->s->istSichtbar(['field' => 'medium', 'op' => 'in', 'value' => ['gas', 'oel']], $a));
        $this->assertFalse($this->s->istSichtbar(['field' => 'medium', 'op' => 'not_in', 'value' => ['gas', 'oel']], $a));
    }

    public function test_numerischer_operator_bei_nichtnumerischem_wert_versteckt(): void
    {
        $this->assertFalse($this->s->istSichtbar(['field' => 'x', 'op' => '>', 'value' => 3], ['x' => 'abc']));
    }

    private function felder(): array
    {
        return [
            ['key' => 'bauart', 'label' => 'Bauart', 'type' => 'select'],
            ['key' => 'sanierung_art', 'label' => 'Sanierung', 'type' => 'select', 'required' => true,
                'visible_if' => ['field' => 'bauart', 'op' => '=', 'value' => 'bestand']],
            // Kind referenziert sanierung_art (Kaskade).
            ['key' => 'sanierung_detail', 'label' => 'Detail', 'type' => 'text',
                'visible_if' => ['field' => 'sanierung_art', 'op' => '=', 'value' => 'daemmung']],
        ];
    }

    public function test_sichtbare_felder_je_antwortstand(): void
    {
        $keys = fn ($a) => array_column($this->s->sichtbareFelder($this->felder(), $a), 'key');

        // Neubau: nur bauart sichtbar.
        $this->assertSame(['bauart'], $keys(['bauart' => 'neubau']));
        // Bestand: bauart + sanierung_art.
        $this->assertSame(['bauart', 'sanierung_art'], $keys(['bauart' => 'bestand']));
    }

    public function test_kaskade_verbirgt_kind_unter_unsichtbarem_elternteil(): void
    {
        // sanierung_art=daemmung wäre erfüllt, aber bauart=neubau versteckt sanierung_art → Kind auch weg.
        $answers = ['bauart' => 'neubau', 'sanierung_art' => 'daemmung'];
        $keys = array_column($this->s->sichtbareFelder($this->felder(), $answers), 'key');
        $this->assertNotContains('sanierung_detail', $keys);
        $this->assertSame(['bauart'], $keys);
    }

    public function test_unsichtbares_pflichtfeld_blockt_nicht(): void
    {
        // Neubau: sanierung_art (required) ist unsichtbar → NICHT in fehlenden Pflichtfeldern.
        $this->assertSame([], $this->s->fehlendePflichtfelder($this->felder(), ['bauart' => 'neubau']));

        // Bestand: sanierung_art sichtbar + leer → blockt.
        $this->assertSame(['sanierung_art'], $this->s->fehlendePflichtfelder($this->felder(), ['bauart' => 'bestand']));

        // Bestand + befüllt → frei.
        $this->assertSame([], $this->s->fehlendePflichtfelder($this->felder(), ['bauart' => 'bestand', 'sanierung_art' => 'daemmung']));
    }

    public function test_alpine_ausdruck(): void
    {
        $this->assertSame('true', $this->s->alpineAusdruck(null));
        $this->assertSame('answers[\'bauart\'] == "bestand"',
            $this->s->alpineAusdruck(['field' => 'bauart', 'op' => '=', 'value' => 'bestand']));
        $this->assertSame('Number(answers[\'personen\']) >= 3',
            $this->s->alpineAusdruck(['field' => 'personen', 'op' => '>=', 'value' => 3]));
        $this->assertSame('["gas","oel"].includes(answers[\'medium\'])',
            $this->s->alpineAusdruck(['field' => 'medium', 'op' => 'in', 'value' => ['gas', 'oel']]));
    }
}
