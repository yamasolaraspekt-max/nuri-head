<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductMasterSet extends Model
{
    use HasFactory;

    protected $fillable = [
        'setname', 'phase_id','article_group', 'price', 'status', 'sub_article', 'material_price', 'employee_price'
    ];



    public function group(){
        return $this->belongsTo('App\Models\GroupSet');
    }


    public function assetSets()
    {
        return $this->hasMany(\App\Models\AssetSet::class, 'master_id');
    }

    public function assets()
    {
        return $this->belongsToMany(\App\Models\Assets::class, 'asset_sets', 'master_id', 'asset_id')
                    ->withPivot(['name','count','total_price'])
                    ->withTimestamps();
    }


     public function articleGroup() { return $this->belongsTo(ArticleGroup::class, 'article_group'); }
    public function phase()        { return $this->belongsTo(TaskPhase::class, 'phase_id'); }

    public function employees() { return $this->hasMany(EmployeeSet::class, 'master_set_id'); }
    public function products()  { return $this->hasMany(AddProductToSet::class, 'master_set_id'); }
    public function subProducts(){ return $this->hasMany(ProductSubSet::class, 'master_set_id'); }

    public function productLists()
    {
        return $this->hasMany(OfferProductList::class, 'master_set_id');
    }
    public function offerAssetLists()
    {
        return $this->hasMany(OfferAssetList::class, 'master_set_id');
    }


 }
