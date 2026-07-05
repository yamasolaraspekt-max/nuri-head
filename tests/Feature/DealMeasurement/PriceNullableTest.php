<?php

namespace Tests\Feature\DealMeasurement;

use App\Http\Controllers\Customer\Offer\DealMaterialListController;
use App\Models\DealMeasurement;
use App\Models\DealMeasurementItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * S-3: deal_measurement_items-Preisspalten nullable — ehrliche NULL-Speicherung von „kein Preis",
 * Bestandspreise unberührt, Leser (Material-Row-Mapper) NULL-sicher.
 */
class PriceNullableTest extends TestCase
{
    use RefreshDatabase;

    private function measurement(): DealMeasurement
    {
        return Schema::withoutForeignKeyConstraints(function () {
            $dealId = DB::table('deals')->insertGetId([
                'customer_id' => 1, 'product_id' => 1, 'alternative_id' => 1, 'service' => 't',
                'employee_id' => 1, 'created_at' => now(), 'updated_at' => now(),
            ]);

            return DealMeasurement::create(['deal_id' => $dealId, 'status' => 'draft']);
        });
    }

    public function test_preisspalten_nehmen_null_an(): void
    {
        $m = $this->measurement();

        $item = DealMeasurementItem::create([
            'deal_measurement_id' => $m->id, 'section_title' => 'Wohnzimmer', 'kind' => 'heizkoerper',
            'name' => 'Heizkörper', 'unit_price' => null, 'purchase_price' => null, 'total_price' => null,
        ]);

        $fresh = $item->fresh();
        $this->assertNull($fresh->unit_price);
        $this->assertNull($fresh->purchase_price);
        $this->assertNull($fresh->total_price);
    }

    public function test_bestandspreise_unberuehrt(): void
    {
        $m = $this->measurement();

        $item = DealMeasurementItem::create([
            'deal_measurement_id' => $m->id, 'name' => 'Rohr', 'kind' => 'product',
            'unit_price' => 10.5, 'purchase_price' => 7.25, 'total_price' => 21.0,
        ]);

        $this->assertEqualsWithDelta(10.5, (float) $item->fresh()->unit_price, 0.001);
    }

    public function test_material_row_mapper_ist_null_sicher(): void
    {
        $m = $this->measurement();
        $item = DealMeasurementItem::create([
            'deal_measurement_id' => $m->id, 'name' => 'Heizkörper', 'kind' => 'heizkoerper',
            'unit_price' => null, 'purchase_price' => null, 'total_price' => null, 'raw_snapshot' => [],
        ]);

        $controller = app(DealMaterialListController::class);
        $ref = new \ReflectionMethod($controller, 'mapDealMeasurementItemToMaterialRow');
        $ref->setAccessible(true);
        $row = $ref->invoke($controller, $item);

        // M5: Mapper ERHÄLT jetzt NULL (ehrliches „kein Preis") statt zu 0 zu koerzieren.
        // Kein Crash. Die Material-Liste-View zeigt ohnehin keinen Preis -> keine „—"-Frontend-Änderung nötig;
        // Offer-Write-back (convertMaterialRowToOfferItem) koerziert null->0 korrekt.
        $this->assertNull($row['price']);
        $this->assertSame('Heizkörper', $row['name']);
    }

    public function test_uebernahme_schreibt_null_preis(): void
    {
        config(['features.heizkoerper' => true]);
        $this->seed(\Database\Seeders\AccessorySeeder::class);
        $spec = \App\Models\RadiatorSpec::create([
            'hersteller' => 'Kermi', 'typ' => '22', 'bauart' => 'kompakt', 'bauhoehe_mm' => 600, 'bautiefe_mm' => 100,
            'q_norm_w_pro_m' => 1666, 'norm_bedingung' => '75/65/20', 'exponent_n' => 1.30,
            'quelle' => 'test', 'imported_from' => 'test', 'aktiv' => true,
        ]);
        $m = $this->measurement();
        $user = \App\Models\User::factory()->create(['password' => 'password', 'name' => '1']); // Deal-Zuständiger

        $this->actingAs($user)->postJson(route('heizkoerper.stueckliste.uebernehmen'), [
            'deal_measurement_id' => $m->id, 'section_title' => 'Wohnzimmer',
            'radiator_spec_id' => $spec->id, 'baulaenge_mm' => 1000, 'anzahl' => 1,
            'ist_ventil_heizkoerper' => false, 'kopf_norm_bestand' => 'M30x1_5',
        ])->assertStatus(201);

        // Ehrliche Speicherung: HK-Zeilen tragen NULL, kein 0,00
        $this->assertGreaterThan(0, DealMeasurementItem::where('deal_measurement_id', $m->id)->whereNull('unit_price')->count());
        $this->assertSame(0, DealMeasurementItem::where('deal_measurement_id', $m->id)->where('kind', 'heizkoerper')->where('unit_price', 0)->count());
    }
}
