<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductWP extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'product_id', 'max_kw', 'min_kw', 'status', 'temp_celsius', 'type'
    ];


        public function product() { return $this->belongsTo(Product::class, 'product_id'); }

}
