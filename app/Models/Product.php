<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductFavorite;
use App\Models\StampArticle;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
    'article_no',
    'sku',                    // newly added
    'ean',
    'brand_id',
    'product',
    'model',
    'category',
    'heatpump_type',          // newly added
    'construction_type',      // newly added
    'refrigerant',            // newly added
    'phase_count',            // newly added
    'scop',                   // newly added
    'noise_level_db',         // newly added
    'roof_type',
    'distributor_id',
    'color',
    'measure_unit',
    'price_unit',
    'package_unit',
    'discount_group',
    'article_group',
    'sub_article', 
    'vat_percent',            // newly added
    'short_description',
    'status'
];



public function customer() { return $this->belongsTo(\App\Models\Customer::class); }

  

public function masterSetCartItems()
{
    return $this->hasMany(\App\Models\MasterSetCartItem::class, 'product_id');
}

  // Product.php
  public function distributor()
  {
      return $this->belongsToMany(Distributor::class, 'distributor_product', 'product_id', 'distributor_id')->withTimestamps();
  }
  

    public function cart(){
        return $this->belongsToMany('App\Models\CustomerCart');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'customer_product_lists');
    }


    public function checklistProduct() {
    return $this->belongsTo(Product::class, 'designation');
    }

    public function distributors()
    {
        return $this->belongsToMany(Distributor::class, 'distributor_prices', 'product_id', 'distributor_id')
                    ->withPivot(['article_no', 'price', 'purchase_price', 'discount_group_id', 'discount_price', 'discount_percent', 'price_date', 'availability'])
                    ->withTimestamps();
    }



    public function distributorLinks()
{
    return $this->belongsToMany(Distributor::class, 'distributor_product', 'product_id', 'distributor_id')
        ->withTimestamps();
}
    public function prices()
    {
        return $this->hasMany(DistributorPrice::class, 'product_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }


public function firstImage()
{
    return $this->hasOne(ProductImage::class, 'product_id')->orderBy('id', 'asc');
} 
    public function images()
{
    return $this->hasMany(ProductImage::class);
}


    // Optional relationships if these are lookup tables:
    public function discountGroup()
    {
        return $this->belongsTo(DiscountGroup::class, 'discount_group');
    }

    public function articleGroup()
    {
        return $this->belongsTo(ArticleGroup::class, 'article_group');
    }

    public function subArticle()
    {
        return $this->belongsTo(SubArticle::class, 'sub_article');
    }

    public function measureUnit()
    {
        return $this->belongsTo(Measure::class, 'measure_unit');
    }


    public function activities()
    {
        return $this->belongsToMany(PhaseActivities::class, 'activity_articles', 'article_id', 'activity_id');
    }


     public function wps()              { return $this->hasMany(ProductWP::class, 'product_id'); }
    public function distributorPrices(){ return $this->hasMany(DistributorPrice::class, 'product_id'); }

 
 
    // Accessor for first image (fallback)
    public function getMainImageAttribute()
    {
        return $this->images()->first()?->image ?? 'placeholder.png';
    }

     public function favorites()
    {
        return $this->hasMany(ProductFavorite::class);
    }

    public function stampArticles()
    {
        return $this->hasMany(StampArticle::class);
    }

    public function isFavoritedByEmployee(?int $employeeId): bool
    {
        if (!$employeeId) {
            return false;
        }

        return $this->favorites()
            ->where('employee_id', $employeeId)
            ->exists();
    }

    public function isStampedByEmployee(?int $employeeId, string $type = 'master'): bool
    {
        if (!$employeeId) {
            return false;
        }

        return $this->stampArticles()
            ->where('employee_id', $employeeId)
            ->where('type', $type)
            ->exists();
    }

    public function measure()
    {
        return $this->belongsTo(\App\Models\Measure::class, 'measure_unit');
    }
 

    public function descriptions()
    {
        return $this->hasMany(ProductDescription::class);
    }
 

    public function getMainImageUrlAttribute(): ?string
    {
        $image = $this->firstImage?->image ?? $this->images()->value('image');

        if (!$image) {
            return null;
        }

        return asset('images/products/' . ltrim($image, '/'));
    }

    public function inventory()
    {
        return $this->hasOne(Inventory::class, 'product_id');
    }

    public function requestOuts()
    {
        return $this->hasMany(InventoryRequestOut::class, 'product_id');
    }

    public function purchaseRequests()
    {
        return $this->hasMany(PurchaseRequest::class, 'product', 'product');
    }

}
