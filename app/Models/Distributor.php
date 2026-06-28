<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Distributor extends Model
{
    use HasFactory;

    protected $fillable = [
        'short_name',
        'created_date',
        'type',
        'salutation',
        'name',
        'name_suffix',
        'street',
        'postal_code',
        'city',
        'phone',
        'email',
        'mobile',
        'account_number',
        'cash_discount',
        'payment_terms',
        'is_hidden',
        'last_changed_at',
        'editor',
        'owner',
        'image',
        'status',
    ];

    protected $casts = [
        'created_date'    => 'datetime',
        'last_changed_at' => 'datetime',
        'is_hidden'       => 'boolean',
        'cash_discount'   => 'decimal:2',
    ];

    public function masterSetCartItems()
    {
        return $this->hasMany(\App\Models\MasterSetCartItem::class, 'distributor_id');
    }

    public function departments()
    {
        return $this->hasMany(DistributorDepartment::class, 'd_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'distributor_prices', 'distributor_id', 'product_id')
            ->withPivot([
                'article_no',
                'price',
                'purchase_price',
                'discount_group_id',
                'discount_price',
                'discount_percent',
                'price_date',
                'availability'
            ])
            ->withTimestamps();
    }

    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class);
    }

    public function prices()
    {
        return $this->hasMany(\App\Models\DistributorPrice::class, 'distributor_id');
    }

    public function offerTemplates()
    {
        return $this->hasMany(\App\Models\OfferTemplate::class, 'distributor_id');
    }

    public function supplierConnections()
    {
        return $this->hasMany(\App\Models\SupplierConnection::class, 'distributor_id');
    }

    public function activeSupplierConnections()
    {
        return $this->hasMany(\App\Models\SupplierConnection::class, 'distributor_id')
            ->where('is_active', true);
    }
}