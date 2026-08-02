<?php

namespace Tests\Feature\Product;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductImageFillableTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_image_uebernimmt_name_per_mass_assignment(): void
    {
        $product = Product::create(['product' => 'Testartikel Bildname']);

        // Spiegelt den Aufruf in SupplierConnectorService::saveProductImage (Zeile ~1345):
        // firstOrCreate mit (product_id, image) als Schluessel und dem Produktnamen als Wert.
        ProductImage::firstOrCreate(
            [
                'product_id' => $product->id,
                'image' => 'supplier-' . $product->id . '-test.jpg',
            ],
            [
                'name' => $product->product,
            ]
        );

        $gespeichert = ProductImage::where('product_id', $product->id)->first();

        $this->assertNotNull($gespeichert);
        $this->assertSame('Testartikel Bildname', $gespeichert->name);
    }
}
