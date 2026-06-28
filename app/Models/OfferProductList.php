<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OfferProductList extends Model
{
    use HasFactory;

    protected $table = 'offer_product_lists';

    protected $fillable = [
        'offer_id',
        'offer_folder_id',
        'master_set_id',
        'name',
        'sku',
        'type',
        'notes',
        'quantity',
        'unit_price',
        'line_net',
        'cost',
        'discount_pct',
        'discount_abs',
        'vat_pct',
        'total_price',
        'gross_price',
        'sort_order',
        'global_discount_pct',
        'global_markup_pct',
        'shipping_net_eur',
        'apply_global_to_shipping',
        'image_name',
        'measure_unit'
    ];

    // ============= Relationships =============

    /**
     * Offer this line belongs to.
     */
    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    /**
     * Folder inside the offer (if applicable).
     */
    public function folder()
    {
        return $this->belongsTo(OfferFolder::class, 'offer_folder_id');
    }

    /**
     * Master set reference.
     */
    public function masterSet()
    {
        return $this->belongsTo(ProductMasterSet::class, 'master_set_id');
    }
}
