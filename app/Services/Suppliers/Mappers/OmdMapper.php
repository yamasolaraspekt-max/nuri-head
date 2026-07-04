<?php

namespace App\Services\Suppliers\Mappers;

use App\Models\Distributor;
use App\Models\Product;
use App\Models\SupplierArticleMap;

/** Stub, W2 — Implementierung folgt bei Portierung. Kein Seiteneffekt, map() gibt null zurück. */
class OmdMapper implements SupplierArticleMapper
{
    public function channel(): string
    {
        return 'omd';
    }

    public function map(array $row, Distributor $d, ?Product $p): ?SupplierArticleMap
    {
        return null;
    }
}
