<?php

namespace App\Services\Suppliers\Mappers;

use App\Models\Distributor;
use App\Models\Product;
use App\Models\SupplierArticleMap;

/**
 * Austauschbarer Lieferantenkanal-Mapper (W2): normalisierte Artikelzeile -> supplier_article_map
 * über den neutralen Schlüssel (hersteller + herst_artikelnr + supplier_channel).
 * Kanäle sind austauschbar hinter diesem Interface; erster produktiver Kanal = IDS (Sonepar).
 */
interface SupplierArticleMapper
{
    /** Kanal-Kennung, z.B. 'ids' | 'omd' | 'datanorm'. */
    public function channel(): string;

    /**
     * Bildet eine normalisierte Artikelzeile auf supplier_article_map ab.
     * Gibt den Datensatz zurück, oder null wenn nicht abbildbar (z.B. hersteller/Product fehlt).
     *
     * @param  array<string, mixed>  $row  normalisierte Zeile (Feldnamen wie im IDS-Import-Flow)
     */
    public function map(array $row, Distributor $d, ?Product $p): ?SupplierArticleMap;
}
