<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterSetComponent extends Model
{
    protected $fillable = [
        'master_set_id',
        'parent_id',
        'product_id',
        'article_no',
        'distributor_article_no',
        'distributor_id',
        'distributor_price_id',
        'unit_price',
        'qty',
        'description',
        'sort_order',
        'purchase_price',
        'margin',
        'skonto',
        'payment_terms',
        'availability',
        'type',
        'is_stammartikel',
        'is_favorite',
        'measure',
        'vpe',
        'price_unit',
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'unit_price' => 'decimal:2',
    ];

    public function masterSet()
    {
        return $this->belongsTo(MasterSet::class, 'master_set_id');
    }

    public function parent()
    {
        return $this->belongsTo(MasterSetComponent::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id', 'id')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
 
    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    public function distributor()
    {
        return $this->belongsTo(Distributor::class, 'distributor_id');
    }

    public function distributorPrice()
    {
        return $this->belongsTo(DistributorPrice::class, 'distributor_price_id');
    }

    public function descriptionVariants()
    {
    return $this->hasMany(\App\Models\MasterSetComponentDescription::class, 'master_set_component_id')
        ->orderBy('context')
        ->orderBy('sort_order')
        ->orderBy('id');
    }

}
