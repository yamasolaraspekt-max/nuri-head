<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterSetCartItem extends Model
{
    protected $fillable = [
        'cart_id',
        'section_id',
        'parent_id',
        'product_id',
        'distributor_id',
        'distributor_price_id',
        'source_type',
        'node_type',
        'title',
        'description',
        'article_no',
        'measure',
        'vpe',
        'price_unit',
        'qty',
        'unit_price',
        'purchase_price',
        'margin',
        'skonto',
        'payment_terms',
        'availability',
        'sort_order',
        'depth',
        'is_stammartikel',
        'is_favorite',
        'is_collapsed',
    ];

    protected $casts = [
        'qty'             => 'decimal:2',
        'unit_price'      => 'decimal:2',
        'purchase_price'  => 'decimal:2',
        'margin'          => 'decimal:2',
        'skonto'          => 'decimal:2',
        'is_stammartikel' => 'boolean',
        'is_favorite'     => 'boolean',
        'is_collapsed'    => 'boolean',
    ];

    public function cart()
    {
        return $this->belongsTo(MasterSetCart::class, 'cart_id');
    }

    public function section()
    {
        return $this->belongsTo(MasterSetCartSection::class, 'section_id');
    }

    public function parent()
    {
        return $this->belongsTo(MasterSetCartItem::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(MasterSetCartItem::class, 'parent_id')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function distributor()
    {
        return $this->belongsTo(Distributor::class, 'distributor_id');
    }

    public function distributorPrice()
    {
        return $this->belongsTo(DistributorPrice::class, 'distributor_price_id');
    }
}