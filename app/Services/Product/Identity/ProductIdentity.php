<?php

namespace App\Services\Product\Identity;

/**
 * Was ein Kanal über einen Artikel behauptet — bereits normalisiert oder roh.
 *
 * Spezifikation: docs/product-data/11-identitaetsspezifikation-paket1-schritt3-4.md §6.
 */
final class ProductIdentity
{
    public function __construct(
        public readonly ?string $gtin                  = null,
        public readonly ?string $manufacturerArticleNo = null,   // -> products.article_no (E5)
        public readonly ?int    $brandId               = null,   // brands.type = manufacturer (E2)
        public readonly ?string $supplierArticleNo     = null,   // -> products.sku (E5)
        public readonly ?int    $distributorId         = null,
        public readonly ?string $model                 = null,   // Typ-/Modellbezeichnung, KEINE Nummer (E6)
        public readonly ?string $name                  = null,
        public readonly string  $channel               = 'unknown', // z.B. 'ids:gconline'
    ) {}
}
