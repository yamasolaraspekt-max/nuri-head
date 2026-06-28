<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerProductInfoMedia extends Model
{
    protected $table = 'customer_product_info_media';

    protected $fillable = [
        'customer_product_info_id',
        'disk',
        'path',
        'stored_name',
        'original_name',
        'mime',
        'size',
        'type',
        'uploaded_by',
        'sort_order',
    ];

    public function productInfo()
    {
        return $this->belongsTo(CustomerProductInfo::class, 'customer_product_info_id');
    }
}
