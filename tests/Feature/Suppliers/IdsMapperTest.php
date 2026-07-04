<?php

namespace Tests\Feature\Suppliers;

use App\Models\Distributor;
use App\Models\Product;
use App\Models\SupplierArticleMap;
use App\Services\Suppliers\Mappers\DatanormMapper;
use App\Services\Suppliers\Mappers\IdsMapper;
use App\Services\Suppliers\Mappers\OmdMapper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * (iii-a) SupplierArticleMapper/IdsMapper — standalone, kein Zugriff auf den produktiven Import-Pfad.
 * Läuft gegen die isolierte Test-DB (RefreshDatabase, phpunit.xml pinnt ticket_testing).
 */
class IdsMapperTest extends TestCase
{
    use RefreshDatabase;

    private function makeBrand(string $name): int
    {
        return DB::table('brands')->insertGetId([
            'name' => $name, 'status' => 1, 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    private function makeProduct(?int $brandId): Product
    {
        $id = DB::table('products')->insertGetId([
            'product' => 'Test-Ventil', 'brand_id' => $brandId, 'created_at' => now(), 'updated_at' => now(),
        ]);

        return Product::findOrFail($id);
    }

    private function makeDistributor(): Distributor
    {
        $id = DB::table('distributors')->insertGetId([
            'status' => 1, 'created_at' => now(), 'updated_at' => now(),
        ]);

        return Distributor::findOrFail($id);
    }

    private function sampleRow(array $over = []): array
    {
        return array_merge([
            'manufacturer_article_no' => '3720-02.000',
            'distributor_article_no' => 'SON-12345',
            'purchase_price' => '18,90',
            'price' => '23.46',
            'image_url' => 'https://example.com/x.jpg',
        ], $over);
    }

    private function skipKey(): string
    {
        return 'supplier_map_skips:ids:' . now()->format('Y-m-d');
    }

    public function test_a_maps_row_field_by_field(): void
    {
        $p = $this->makeProduct($this->makeBrand('IMI Heimeier'));
        $d = $this->makeDistributor();

        $map = (new IdsMapper)->map($this->sampleRow(), $d, $p);

        $this->assertNotNull($map);
        $this->assertSame('IMI Heimeier', $map->hersteller);
        $this->assertSame('3720-02.000', $map->herst_artikelnr);
        $this->assertSame('ids', $map->supplier_channel);
        $this->assertSame('SON-12345', $map->lieferanten_artikelnr);
        $this->assertSame($d->id, $map->distributor_id);
        $this->assertSame($p->id, $map->product_id);
        $this->assertSame('18.90', $map->ek_preis);
        $this->assertSame('23.46', $map->vk_preis);
        $this->assertSame('https://example.com/x.jpg', $map->bild_url);
        $this->assertNotNull($map->last_synced_at);
        $this->assertSame(1, SupplierArticleMap::count());
    }

    public function test_b_idempotent_updates_same_row(): void
    {
        $p = $this->makeProduct($this->makeBrand('Oventrop'));
        $d = $this->makeDistributor();
        $m = new IdsMapper;

        $first = $m->map($this->sampleRow(['manufacturer_article_no' => '1183804']), $d, $p);
        DB::table('supplier_article_map')->where('id', $first->id)->update(['last_synced_at' => '2000-01-01 00:00:00']);

        $second = $m->map($this->sampleRow(['manufacturer_article_no' => '1183804']), $d, $p);

        $this->assertSame(1, SupplierArticleMap::count());
        $this->assertSame($first->id, $second->id);
        $this->assertGreaterThan(2000, $second->fresh()->last_synced_at->year);
    }

    public function test_c_empty_image_url_is_null(): void
    {
        $p = $this->makeProduct($this->makeBrand('Danfoss'));
        $d = $this->makeDistributor();

        $map = (new IdsMapper)->map($this->sampleRow(['image_url' => '']), $d, $p);

        $this->assertNotNull($map);
        $this->assertNull($map->bild_url);
    }

    public function test_d_missing_hersteller_skips_and_counts(): void
    {
        Cache::forget($this->skipKey());
        $p = $this->makeProduct(null); // kein brand -> kein hersteller
        $d = $this->makeDistributor();

        $map = (new IdsMapper)->map($this->sampleRow(), $d, $p);

        $this->assertNull($map);
        $this->assertSame(0, SupplierArticleMap::count());
        $this->assertSame(1, (int) Cache::get($this->skipKey()));
    }

    public function test_e_null_product_skips_and_counts(): void
    {
        Cache::forget($this->skipKey());
        $d = $this->makeDistributor();

        $map = (new IdsMapper)->map($this->sampleRow(), $d, null);

        $this->assertNull($map);
        $this->assertSame(0, SupplierArticleMap::count());
        $this->assertSame(1, (int) Cache::get($this->skipKey()));
    }

    public function test_f_stub_mappers_return_null_no_side_effects(): void
    {
        $p = $this->makeProduct($this->makeBrand('X'));
        $d = $this->makeDistributor();
        $row = $this->sampleRow();

        $this->assertNull((new OmdMapper)->map($row, $d, $p));
        $this->assertNull((new DatanormMapper)->map($row, $d, $p));
        $this->assertSame(0, SupplierArticleMap::count());
        $this->assertSame('omd', (new OmdMapper)->channel());
        $this->assertSame('datanorm', (new DatanormMapper)->channel());
    }

    public function test_g_decimal_precision(): void
    {
        $p = $this->makeProduct($this->makeBrand('Y'));
        $d = $this->makeDistributor();

        $map = (new IdsMapper)->map($this->sampleRow(['purchase_price' => '1234.56', 'price' => '0,07']), $d, $p);

        $this->assertSame('1234.56', $map->ek_preis);
        $this->assertSame('0.07', $map->vk_preis);
    }
}
