<?php

namespace Tests\Feature\Heizkoerper;

use App\Models\RadiatorSpec;
use App\Services\Heizkoerper\RadiatorCatalogAdapter;
use App\Services\Heizkoerper\RadiatorPerformanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * (M3 iv-a) RadiatorCatalogAdapter gegen ticket_testing (product_radiator_specs nur dort bis M5).
 * Beweist die Ableitung q_norm_w = q_norm_w_pro_m × Baulänge × Anzahl und die Naht zum reinen Kern.
 */
class RadiatorCatalogAdapterTest extends TestCase
{
    use RefreshDatabase;

    private function spec(): RadiatorSpec
    {
        return RadiatorSpec::create([
            'hersteller' => 'Kermi', 'typ' => 'Kompakt Typ 22', 'bauart' => 'kompakt',
            'bauhoehe_mm' => 600, 'bautiefe_mm' => 100,
            'q_norm_w_pro_m' => 1666, 'norm_bedingung' => '75/65/20', 'exponent_n' => 1.31,
            'quelle' => 'test', 'imported_from' => 'test', 'aktiv' => true,
        ]);
    }

    public function test_derives_q_norm_w_from_spec_length_and_count(): void
    {
        $spec = $this->spec();
        $adapter = new RadiatorCatalogAdapter;

        $entry = $adapter->toQRealEntry($spec, 1.0, 1);
        $this->assertEqualsWithDelta(1666.0, $entry['q_norm_w'], 0.01);
        $this->assertEqualsWithDelta(1.31, $entry['exponent_n'], 0.001);
        $this->assertSame('75/65/20', $entry['norm_bedingung']);

        // Baulänge × Anzahl: 1666 × 2 × 3 = 9996
        $this->assertEqualsWithDelta(9996.0, $adapter->toQRealEntry($spec, 2.0, 3)['q_norm_w'], 0.01);
    }

    public function test_adapter_output_feeds_performance_service(): void
    {
        $spec = $this->spec();
        $entry = (new RadiatorCatalogAdapter)->toQRealEntry($spec, 1.0, 1);

        // Am Normpunkt (75/65/20, Spreizung 10) muss qReal ≈ q_norm_w liefern.
        $q = (new RadiatorPerformanceService)->qReal([$entry], 75, 20, 10);
        $this->assertEqualsWithDelta(1666, $q, 5);
    }
}
