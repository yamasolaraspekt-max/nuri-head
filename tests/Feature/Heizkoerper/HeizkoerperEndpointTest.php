<?php

namespace Tests\Feature\Heizkoerper;

use App\Models\RadiatorSpec;
use App\Models\User;
use Database\Seeders\AccessorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * (M4-a v-a) Rechen-/Kompatibilitäts-Endpunkte hinter Feature-Flag.
 * Durchstich der abgenommenen Kerne über HTTP; Flag-Gate; Validierung. Gegen ticket_testing.
 */
class HeizkoerperEndpointTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['features.heizkoerper' => true]); // Modul in Tests AN (prod: OFF)
    }

    private function spec2000(): RadiatorSpec
    {
        // q_norm_w_pro_m 2000 × Baulänge 1 m × 1 → q_norm_w 2000 (Parität-Bedingung, exponent 1.30)
        return RadiatorSpec::create([
            'hersteller' => 'Kermi', 'typ' => 'Test 22', 'bauart' => 'kompakt',
            'bauhoehe_mm' => 600, 'bautiefe_mm' => 100,
            'q_norm_w_pro_m' => 2000, 'norm_bedingung' => '75/65/20', 'exponent_n' => 1.30,
            'quelle' => 'test', 'imported_from' => 'test', 'aktiv' => true,
        ]);
    }

    /** EN-442-Durchstich: 2000-W-HK bei 45 °C/7 K → q_real ≈ 663 (abgenommener Paritätswert über HTTP). */
    public function test_berechnen_durchstich_663(): void
    {
        $spec = $this->spec2000();

        $r = $this->actingAs(User::factory()->create(['password' => 'password']))->postJson(route('heizkoerper.berechnen'), [
            'radiator_spec_id' => $spec->id,
            'baulaenge_mm' => 1000,
            'anzahl' => 1,
            'vorlauf' => 45,
            'raumtemp' => 20,
            'spreizung' => 7,
            'heizlast_w' => 663,
        ]);

        $r->assertOk();
        $this->assertEqualsWithDelta(2000, $r->json('q_norm_w'), 1);
        $this->assertEqualsWithDelta(663, $r->json('q_real'), 20);
        $this->assertCount(5, $r->json('en442_tabelle'));
        $this->assertSame(75, $r->json('en442_tabelle.0.vorlauf'));
        $this->assertSame(35, $r->json('en442_tabelle.4.vorlauf'));
        $this->assertContains($r->json('ampel'), ['gruen', 'gelb', 'rot']);
        $this->assertNotNull($r->json('min_vorlauf'));
    }

    /** Kompatibilität-Endpunkt: leere valve_insert_compatibility → 'regel-kandidaten' + Kandidaten. */
    public function test_kompatibilitaet_datenqualitaet_regel_kandidaten(): void
    {
        $this->seed(AccessorySeeder::class);

        $r = $this->actingAs(User::factory()->create(['password' => 'password']))->postJson(route('heizkoerper.kompatibilitaet'), [
            'ist_ventil_heizkoerper' => false,
            'anschluss_fuehrung' => 'zweirohr',
            'kopf_norm_bestand' => 'M30x1_5',
            'heizlast_w' => 1000,
            'spreizung' => 7,
        ]);

        $r->assertOk();
        $this->assertSame('regel-kandidaten', $r->json('datenqualitaet'));
        $this->assertNotEmpty($r->json('positionen'));
        $this->assertSame(4, $r->json('voreinstellstufe')); // §5.5 via HydraulicService (122,8 → kv 0,39 → Stufe 4)
    }

    /**
     * Flag OFF → 404 (für den berechtigten/authentifizierten Nutzer) und garantiert KEINE Query auf
     * HK-Tabellen (Verschärfung W-Env). Hinweis: unauthentifiziert greift Laravels Middleware-Priorität
     * ('auth' vor Custom) → 401; beides blockt vor jeder HK-Query. Getestet wird das Flag-Gate selbst.
     */
    public function test_flag_off_liefert_404_ohne_hk_query(): void
    {
        config(['features.heizkoerper' => false]);
        $this->actingAs(User::factory()->create(['password' => 'password']));

        $queries = [];
        DB::listen(function ($q) use (&$queries) {
            $queries[] = $q->sql;
        });

        $this->postJson(route('heizkoerper.berechnen'), ['vorlauf' => 45, 'spreizung' => 7])->assertNotFound();

        $sql = implode(' | ', $queries);
        foreach (['product_radiator_specs', 'accessories', 'valve_insert_compatibility', 'radiator_installations'] as $tabelle) {
            $this->assertStringNotContainsString($tabelle, $sql, "HK-Tabelle {$tabelle} wurde trotz Flag-OFF angefragt");
        }
    }

    /** Fehlende Pflichtfelder → 422. */
    public function test_berechnen_validierung_422(): void
    {
        $r = $this->actingAs(User::factory()->create(['password' => 'password']))->postJson(route('heizkoerper.berechnen'), [
            'radiator_spec_id' => 999999, // existiert nicht + vorlauf/spreizung fehlen
        ]);

        $r->assertStatus(422);
    }
}
