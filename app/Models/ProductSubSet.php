<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSubSet extends Model
{
    use HasFactory;
     protected $fillable = [
        'master_set_id', 'main_product', 'product_id', 'product_count', 'measure_unit',  'retail_price'  , 'discount_group' , 'purchase_price' , 'total', 'distributor_id'
    ];


     public function master()   { return $this->belongsTo(ProductMasterSet::class,'master_set_id'); }
    public function product()  { return $this->belongsTo(Product::class); }
    public function main()     { return $this->belongsTo(AddProductToSet::class,'main_product'); }

 
}
