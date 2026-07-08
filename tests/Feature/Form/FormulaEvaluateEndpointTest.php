<?php

namespace Tests\Feature\Form;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * FS-07 — eval-freier Server-Endpoint (Ersatz für `new Function`): berechnet Sichtbarkeit +
 * Calculation-Werte serverseitig gegen das v2-Schema. Kein Client-eval mehr im Auswertungspfad.
 */
class FormulaEvaluateEndpointTest extends TestCase
{
    use DatabaseTransactions;

    private function felder(): array
    {
        return [
            ['key' => 'laenge', 'label' => 'Länge', 'type' => 'length', 'unit' => 'm', 'decimals' => 2],
            ['key' => 'breite', 'label' => 'Breite', 'type' => 'length', 'unit' => 'm', 'decimals' => 2],
            ['key' => 'flaeche', 'label' => 'Fläche', 'type' => 'calculation', 'unit' => 'm2', 'decimals' => 2,
                'calculation' => ['formel' => 'FLAECHE(laenge,breite)', 'rundung' => 'kaufmaennisch']],
            ['key' => 'zusatz', 'label' => 'Zusatz', 'type' => 'text', 'required' => true,
                'visible_if' => ['field' => 'laenge', 'op' => '>', 'value' => 100]],
        ];
    }

    private function sende(array $payload)
    {
        $user = User::factory()->create(['password' => 'password', 'name' => 'formeval', 'is_admin' => true]);

        return $this->actingAs($user)->postJson(route('product.formula.evaluate'), $payload);
    }

    public function test_berechnet_flaeche_serverseitig_ohne_client_eval(): void
    {
        $r = $this->sende(['fields' => $this->felder(), 'answers' => ['laenge' => 5, 'breite' => 4]]);
        $r->assertOk();
        $r->assertJsonPath('calculations.flaeche.value', 20);
        // 'zusatz' ist unsichtbar (laenge 5 <= 100) → nicht in visible, blockt nicht.
        $this->assertNotContains('zusatz', $r->json('visible'));
        $this->assertSame([], $r->json('missing_required'));
    }

    public function test_unsichtbare_calculation_wird_nicht_berechnet(): void
    {
        $felder = $this->felder();
        $felder[2]['visible_if'] = ['field' => 'laenge', 'op' => '>', 'value' => 1000];
        $r = $this->sende(['fields' => $felder, 'answers' => ['laenge' => 5, 'breite' => 4]]);
        $r->assertOk();
        $this->assertArrayNotHasKey('flaeche', $r->json('calculations'));
    }

    public function test_sichtbares_pflichtfeld_wird_als_fehlend_gemeldet(): void
    {
        // laenge=200 > 100 → 'zusatz' sichtbar + leer → fehlend.
        $r = $this->sende(['fields' => $this->felder(), 'answers' => ['laenge' => 200, 'breite' => 4]]);
        $r->assertOk();
        $this->assertContains('zusatz', $r->json('visible'));
        $this->assertContains('zusatz', $r->json('missing_required'));
    }

    public function test_schema_verstoss_liefert_422(): void
    {
        $r = $this->sende(['fields' => [['label' => 'ohne key', 'type' => 'text']], 'answers' => []]);
        $r->assertStatus(422);
        $r->assertJsonStructure(['message', 'errors']);
    }
}
