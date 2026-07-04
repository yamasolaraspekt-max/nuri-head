<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/** Zubehör-Stammdaten (Ventil/Kopf/Adapter/…). kvs_werte als JSON. Reine Stammdaten. */
class Accessory extends Model
{
    use HasFactory;

    protected $fillable = [
        'accessory_category_id',
        'hersteller',
        'herst_artikelnr',
        'name',
        'typ',
        'dn',
        'kvs_werte',
        'kopf_anschluss_norm',
        'einrohr_tauglich',
        'voreinstellbar',
        'product_id',
        'quelle',
        'imported_from',
        'aktiv',
    ];

    protected $casts = [
        'dn' => 'integer',
        'kvs_werte' => 'array',
        'einrohr_tauglich' => 'boolean',
        'voreinstellbar' => 'boolean',
        'aktiv' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(AccessoryCategory::class, 'accessory_category_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
