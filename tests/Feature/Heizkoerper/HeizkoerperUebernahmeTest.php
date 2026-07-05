<?php

namespace Tests\Feature\Heizkoerper;

use App\Http\Controllers\Customer\Offer\DealMaterialListController;
use App\Models\DealMeasurement;
use App\Models\DealMeasurementItem;
use App\Models\RadiatorSpec;
use App\Models\User;
use Database\Seeders\AccessorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * (M4-a v-c-2) Übernahme-Endpunkt: additive deal_measurement_items (kind='heizkoerper'),
 * Replace-per-Raum, Provenance, Bestands-Guards. Integration + Grenz-Regression gegen die
 * bestehende Material-Liste (DealMaterialListController, nur gelesen).
 */
class HeizkoerperUebernahmeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['features.heizkoerper' => true]);
        $this->seed(AccessorySeeder::class);
    }

    private function user(): User
    {
        // S-1a: name='1' = Employee-ID = deal.employee_id der measurement()-Fixture -> Deal-Zuständiger, write erlaubt.
        return User::factory()->create(['password' => 'password', 'name' => '1']);
    }

    private function measurement(string $status = 'draft'): DealMeasurement
    {
        return Schema::withoutForeignKeyConstraints(function () use ($status) {
            $dealId = DB::table('deals')->insertGetId([
                'customer_id' => 1, 'product_id' => 1, 'alternative_id' => 1,
                'service' => 'test', 'employee_id' => 1,
                'created_at' => now(), 'updated_at' => now(),
            ]);

            return DealMeasurement::create(['deal_id' => $dealId, 'offer_id' => 77, 'status' => $status]);
        });
    }

    private function spec(): RadiatorSpec
    {
        return RadiatorSpec::create([
            'hersteller' => 'Kermi', 'typ' => 'Kompakt 22', 'bauart' => 'kompakt',
            'bauhoehe_mm' => 600, 'bautiefe_mm' => 100,
            'q_norm_w_pro_m' => 1666, 'norm_bedingung' => '75/65/20', 'exponent_n' => 1.30,
            'quelle' => 'test', 'imported_from' => 'test', 'aktiv' => true,
        ]);
    }

    private function payload(DealMeasurement $m, array $over = []): array
    {
        return array_merge([
            'deal_measurement_id' => $m->id,
            'section_title' => 'Wohnzimmer',
            'radiator_spec_id' => $this->spec()->id,
            'baulaenge_mm' => 1000,
            'anzahl' => 2,
            'vorlauf' => 55, 'spreizung' => 10,
            'ist_ventil_heizkoerper' => false,
            'anschluss_fuehrung' => 'zweirohr',
            'kopf_norm_bestand' => 'M30x1_5',
        ], $over);
    }

    public function test_uebernahme_schreibt_provenance_je_spalte(): void
    {
        $m = $this->measurement();

        $r = $this->actingAs($this->user())->postJson(route('heizkoerper.stueckliste.uebernehmen'), $this->payload($m));

        $r->assertStatus(201);
        $r->assertJson(['success' => true, 'datenqualitaet' => 'regel-kandidaten', 'section_title' => 'Wohnzimmer']);

        // HK-Position vorhanden mit voller Provenance
        $hk = DealMeasurementItem::where('deal_measurement_id', $m->id)
            ->where('kind', 'heizkoerper')->where('section_title', 'Wohnzimmer')
            ->where('item_type', 'heizkoerper')->get();

        $this->assertGreaterThanOrEqual(2, $hk->count()); // HK + mind. 1 Zubehör
        $this->assertEqualsCanonicalizing(['heizkoerper'], $hk->pluck('kind')->unique()->values()->all());
        $this->assertSame((string) $m->deal_id, (string) $hk->first()->deal_id);
        $this->assertSame(77, (int) $hk->first()->offer_id);

        $radiator = $hk->firstWhere('raw_snapshot.kategorie', 'heizkoerper');
        $this->assertNotNull($radiator);
        $this->assertStringContainsString('Heizkörper', $radiator->name);
        $this->assertNull($radiator->article_no);                                  // regel-kandidaten -> keine SKU
        $this->assertSame('regel-kandidaten', $radiator->raw_snapshot['datenqualitaet']);
        $this->assertSame('heizkoerper', $radiator->raw_snapshot['herkunft']);
        $this->assertFalse($radiator->raw_snapshot['preis_bekannt']);
        $this->assertSame(55, (int) $radiator->raw_snapshot['eingabe']['vorlauf']);
        $this->assertEquals(2, (int) $radiator->qty_final);                        // anzahl
    }

    public function test_replace_per_raum_idempotent(): void
    {
        $m = $this->measurement();
        $user = $this->user();

        $this->actingAs($user)->postJson(route('heizkoerper.stueckliste.uebernehmen'), $this->payload($m))->assertStatus(201);
        $ersteAnzahl = DealMeasurementItem::where('deal_measurement_id', $m->id)->where('kind', 'heizkoerper')->count();

        // Zweite Übernahme desselben Raums -> gleicher frischer Satz, keine Dubletten
        $this->actingAs($user)->postJson(route('heizkoerper.stueckliste.uebernehmen'), $this->payload($m))->assertStatus(201);
        $zweiteAnzahl = DealMeasurementItem::where('deal_measurement_id', $m->id)->where('kind', 'heizkoerper')->count();

        $this->assertSame($ersteAnzahl, $zweiteAnzahl);
    }

    public function test_replace_laesst_fremdzeilen_und_andere_raeume_ueberleben(): void
    {
        $m = $this->measurement();

        // Fremdzeilen im SELBEN Raum + HK-Zeile in einem anderen Raum
        $labor = DealMeasurementItem::create(['deal_measurement_id' => $m->id, 'section_title' => 'Wohnzimmer', 'kind' => 'labor', 'name' => 'Montagestunde']);
        $produkt = DealMeasurementItem::create(['deal_measurement_id' => $m->id, 'section_title' => 'Wohnzimmer', 'kind' => 'product', 'name' => 'Kupferrohr', 'product_id' => 999]);
        $andererRaum = DealMeasurementItem::create(['deal_measurement_id' => $m->id, 'section_title' => 'Bad', 'kind' => 'heizkoerper', 'name' => 'Heizkörper Bad']);

        $this->actingAs($this->user())->postJson(route('heizkoerper.stueckliste.uebernehmen'), $this->payload($m))->assertStatus(201);

        // Fremdzeilen im Wohnzimmer unberührt (nicht soft-deleted)
        $this->assertNotSoftDeleted('deal_measurement_items', ['id' => $labor->id]);
        $this->assertNotSoftDeleted('deal_measurement_items', ['id' => $produkt->id]);
        // HK-Zeile eines ANDEREN Raums unberührt
        $this->assertNotSoftDeleted('deal_measurement_items', ['id' => $andererRaum->id]);
    }

    public function test_integration_material_liste_zeigt_hk_und_labor_filter_intakt(): void
    {
        $m = $this->measurement();
        DealMeasurementItem::create(['deal_measurement_id' => $m->id, 'section_title' => 'Wohnzimmer', 'kind' => 'labor', 'name' => 'Montagestunde']);

        $this->actingAs($this->user())->postJson(route('heizkoerper.stueckliste.uebernehmen'), $this->payload($m))->assertStatus(201);

        // DealMaterialListController (nur gelesen) via Reflection auf die deal_measurement_items-Quelle
        $controller = app(DealMaterialListController::class);
        $ref = new \ReflectionMethod($controller, 'extractMaterialsFromDealMeasurementItems');
        $ref->setAccessible(true);
        $rows = collect($ref->invoke($controller, $m->fresh()));

        $names = $rows->pluck('name')->implode(' | ');
        $this->assertStringContainsString('Heizkörper', $names);                   // HK-Zeilen inkludiert
        $this->assertStringNotContainsString('Montagestunde', $names);             // labor bleibt ausgefiltert
    }

    public function test_flag_off_404(): void
    {
        config(['features.heizkoerper' => false]);
        $m = $this->measurement();
        $this->actingAs($this->user())->postJson(route('heizkoerper.stueckliste.uebernehmen'), $this->payload($m))->assertNotFound();
    }

    public function test_completed_measurement_423(): void
    {
        $m = $this->measurement('completed');
        $this->actingAs($this->user())->postJson(route('heizkoerper.stueckliste.uebernehmen'), $this->payload($m))->assertStatus(423);
    }

    public function test_ohne_deal_measurement_id_422(): void
    {
        $this->actingAs($this->user())->postJson(route('heizkoerper.stueckliste.uebernehmen'), ['section_title' => 'Wohnzimmer'])->assertStatus(422);
    }

    public function test_leerer_positionssatz_422(): void
    {
        $m = $this->measurement();
        // kein Spec + einrohr (alle geseedeten Armaturen sind zweirohr) -> weder HK- noch Zubehör-Position
        $r = $this->actingAs($this->user())->postJson(route('heizkoerper.stueckliste.uebernehmen'), [
            'deal_measurement_id' => $m->id, 'section_title' => 'Wohnzimmer',
            'anschluss_fuehrung' => 'einrohr', 'ist_ventil_heizkoerper' => false,
        ]);
        $r->assertStatus(422);
        $this->assertSame(0, DealMeasurementItem::where('deal_measurement_id', $m->id)->count());
    }
}
