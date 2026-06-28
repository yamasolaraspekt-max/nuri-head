<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $table = 'assets';

      protected $guarded = [];
  protected $fillable = [
        'serial_no','article_no','item','model','category','parent_id',
        'purchase_price','purchase_type','leasing_from','leasing_date','leasing_end_date','leasing_price',
        'purchase_date','expire_date','location','description','image','quantity','status','pdf',
        'handover_id','branch_id','used_for',
    ];

    protected $casts = [
        'leasing_date'     => 'date',
        'leasing_end_date' => 'date',
        'purchase_date'    => 'date',
        'expire_date'      => 'date',
        'purchase_price'   => 'decimal:2',
        'leasing_price'    => 'decimal:2',
    ];

    // ── Relationships ──────────────────────────────────────────────

    // Branch this asset belongs to
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    // The employee who handed over / is responsible on creation
    public function handover()
    {
        return $this->belongsTo(Employee::class, 'handover_id');
    }

    // Article group / Gewerke
    public function articleGroup()
    {
        // column name is "used_for" (non-standard), so specify it
        return $this->belongsTo(ArticleGroup::class, 'used_for');
    }

    // Parent asset (self reference)
    public function parent()
    {
        return $this->belongsTo(Asset::class, 'parent_id');
    }

    // Child asset (self reference)
    public function children()
    {
        return $this->hasMany(Asset::class, 'parent_id');
    }

    // Optional: if you have an installments table like asset_installments.asset_id
    public function installments()
    {
        return $this->hasMany(AssetInstallment::class, 'asset_id');
    }

    // Eager-load common relations to avoid N+1 in index views
    protected $with = ['branch', 'articleGroup', 'parent'];


    public function assetets()
    {
        return $this->hasMany(\App\Models\Assetet::class);
    }

    public function productMasterSets()
    {
        return $this->belongsToMany(\App\Models\ProductMasterSet::class, 'asset_sets', 'asset_id', 'master_id')
                    ->withPivot(['name','count','total_price'])
                    ->withTimestamps();
    }

    public function offerAssetLists()
    {
        return $this->hasMany(OfferAssetList::class, 'asset_id');
    }

}
