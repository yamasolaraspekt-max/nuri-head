<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DealMeasurementItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'deal_measurement_id',
        'deal_id',
        'offer_id',
        'offer_detail_id',
        'section_title',
        'sort_order',
        'depth',
        'item_type',
        'kind',
        'master_set_id',
        'master_set_component_id',
        'product_id',
        'component_id',
        'article_no',
        'distributor_article_no',
        'distributor_id',
        'distributor_name',
        'name',
        'description',
        'image',
        'qty_offer',
        'qty_measurement',
        'qty_final',
        'unit',
        'measure',
        'unit_price',
        'purchase_price',
        'total_price',
        'order_status',
        'stock_allocation',
        'raw_snapshot',
        'is_checked',
        'note',
        'updated_by',

    ];

    protected $casts = [
        'stock_allocation' => 'array',
        'raw_snapshot' => 'array',
        'is_checked' => 'boolean',
        'qty_offer' => 'decimal:4',
        'qty_measurement' => 'decimal:4',
        'qty_final' => 'decimal:4',
        'unit_price' => 'decimal:4',
        'purchase_price' => 'decimal:4',
        'total_price' => 'decimal:4',
    ];

    public function measurement()
    {
        return $this->belongsTo(DealMeasurement::class, 'deal_measurement_id');
    }

    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}