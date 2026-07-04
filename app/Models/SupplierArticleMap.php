<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/** Neutraler Lieferanten-Artikel-Map (hersteller + herst_artikelnr, je Kanal). Reine Stammdaten. */
class SupplierArticleMap extends Model
{
    use HasFactory;

    protected $table = 'supplier_article_map';

    protected $fillable = [
        'hersteller',
        'herst_artikelnr',
        'supplier_channel',
        'distributor_id',
        'lieferanten_artikelnr',
        'ek_preis',
        'vk_preis',
        'bild_url',
        'product_id',
        'accessory_id',
        'last_synced_at',
    ];

    protected $casts = [
        'ek_preis' => 'decimal:2',
        'vk_preis' => 'decimal:2',
        'last_synced_at' => 'datetime',
    ];

    public function distributor()
    {
        return $this->belongsTo(Distributor::class, 'distributor_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function accessory()
    {
        return $this->belongsTo(Accessory::class, 'accessory_id', 'id');
    }
}
