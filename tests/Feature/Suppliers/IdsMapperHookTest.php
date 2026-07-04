<?php

namespace Tests\Feature\Suppliers;

use App\Models\Distributor;
use App\Models\DistributorPrice;
use App\Models\Product;
use App\Models\SupplierArticleMap;
use App\Models\SupplierConnection;
use App\Models\SupplierImportLog;
use App\Services\Suppliers\Mappers\MapperRegistry;
use App\Services\Suppliers\Mappers\SupplierArticleMapper;
use App\Services\Suppliers\SupplierConnectorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * (iii-b) Live-Hook post-commit in importReviewedItems: der Import befüllt supplier_article_map
 * über die MapperRegistry und bleibt gegen Mapper-Fehler strukturell unantastbar.
 */
class IdsMapperHookTest extends TestCase
{
    use RefreshDatabase;

    private function brand(string $name): int
    {
        return DB::table('brands')->insertGetId(['name' => $name, 'status' => 1, 'created_at' => now(), 'updated_at' => now()]);
    }

    private function distributor(): Distributor
    {
        $id = DB::table('distributors')->insertGetId(['status' => 1, 'created_at' => now(), 'updated_at' => now()]);

        return Distributor::findOrFail($id);
    }

    private function connection(string $connectorType, int $distributorId): SupplierConnection
    {
        $id = DB::table('supplier_connections')->insertGetId([
            'name' => 'Test ' . $connectorType,
            'supplier_key' => 'test_' . $connectorType,
            'connector_type' => $connectorType,
            'distributor_id' => $distributorId,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        return SupplierConnection::findOrFail($id);
    }

    private function log(int $connectionId): SupplierImportLog
    {
        $id = DB::table('supplier_import_logs')->insertGetId([
            'supplier_connection_id' => $connectionId,
            'status' => 'received',
            'payload' => json_encode(['items' => []]),
            'created_at' => now(), 'updated_at' => now(),
        ]);

        return SupplierImportLog::findOrFail($id);
    }

    private function reviewedRows(array $over = []): array
    {
        return [array_merge([
            'import' => 1,
            'product_title' => 'V-exact II',
            'distributor_article_no' => 'SON-1',
            'manufacturer_article_no' => '3720-02.000',
            'ean' => '',
            'price' => '23.46',
            'purchase_price' => '18,90',
            'availability' => '', 'measure_unit' => '', 'short_description' => '',
            'image_url' => 'https://x/y.jpg', 'vat_percent' => 19,
        ], $over)];
    }

    private function service(): SupplierConnectorService
    {
        return app(SupplierConnectorService::class);
    }

    public function test_1_happy_ids_creates_map_and_price(): void
    {
        $b = $this->brand('IMI Heimeier');
        $d = $this->distributor();
        $c = $this->connection('ids', $d->id);

        $this->service()->importReviewedItems($c, $this->log($c->id), $d->id, $b, $this->reviewedRows());

        $this->assertSame(1, DistributorPrice::count());
        $this->assertSame(1, SupplierArticleMap::count());
        $m = SupplierArticleMap::first();
        $this->assertSame('ids', $m->supplier_channel);
        $this->assertSame('3720-02.000', $m->herst_artikelnr);
        $this->assertSame('IMI Heimeier', $m->hersteller);
    }

    public function test_2_oci_channel_maps_too(): void
    {
        $b = $this->brand('Oventrop');
        $d = $this->distributor();
        $c = $this->connection('oci', $d->id);

        $this->service()->importReviewedItems($c, $this->log($c->id), $d->id, $b, $this->reviewedRows(['manufacturer_article_no' => '1183804']));

        $this->assertSame(1, SupplierArticleMap::count());
        $this->assertSame('1183804', SupplierArticleMap::first()->herst_artikelnr);
    }

    public function test_3_mapper_throws_import_survives(): void
    {
        $this->app->singleton(MapperRegistry::class, function () {
            return new class extends MapperRegistry
            {
                public function resolve(?string $connectorType): ?SupplierArticleMapper
                {
                    return new class implements SupplierArticleMapper
                    {
                        public function channel(): string
                        {
                            return 'ids';
                        }

                        public function map(array $row, Distributor $d, ?Product $p): ?SupplierArticleMap
                        {
                            throw new \RuntimeException('boom');
                        }
                    };
                }
            };
        });

        $b = $this->brand('Danfoss');
        $d = $this->distributor();
        $c = $this->connection('ids', $d->id);

        $this->service()->importReviewedItems($c, $this->log($c->id), $d->id, $b, $this->reviewedRows());

        $this->assertSame(1, DistributorPrice::count());   // Import ueberlebt (Tx committed)
        $this->assertSame(0, SupplierArticleMap::count()); // Map leer, Fehler nur geloggt
    }

    public function test_4_unknown_connector_type_is_noop(): void
    {
        $b = $this->brand('X');
        $d = $this->distributor();
        $c = $this->connection('csv', $d->id);

        $this->service()->importReviewedItems($c, $this->log($c->id), $d->id, $b, $this->reviewedRows());

        $this->assertSame(1, DistributorPrice::count());
        $this->assertSame(0, SupplierArticleMap::count());
    }

    public function test_5_product_without_brand_skips_and_counts(): void
    {
        $key = 'supplier_map_skips:ids:' . now()->format('Y-m-d');
        Cache::forget($key);

        $d = $this->distributor();
        $c = $this->connection('ids', $d->id);

        // defaultBrandId = null, Zeile ohne brand_id => Product ohne brand => Skip
        $this->service()->importReviewedItems($c, $this->log($c->id), $d->id, null, $this->reviewedRows());

        $this->assertSame(1, DistributorPrice::count());
        $this->assertSame(0, SupplierArticleMap::count());
        $this->assertSame(1, (int) Cache::get($key));
    }
}
