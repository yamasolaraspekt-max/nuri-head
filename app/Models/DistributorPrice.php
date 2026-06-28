<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistributorPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'distributor_id', 'product_id', 'discount_group_id',
        'article_no', 'discount_price', 'discount_percent',
        'price', 'purchase_price', 'price_date', 'availability', 'status', 'creator_id', 'notice'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'purchase_price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'price_date' => 'date',
    ];
    public function masterSetCartItems()
    {
        return $this->hasMany(\App\Models\MasterSetCartItem::class, 'distributor_price_id');
    }

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }


    public function discountGroup()
    {
        return $this->belongsTo(DiscountGroup::class, 'discount_group_id');
    }

        public function product()     { return $this->belongsTo(Product::class); }
 }
