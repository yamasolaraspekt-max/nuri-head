<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterSetCart extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'creator_id',
        'article_group_id',
        'target_master_set_id',
        'mode',
        'name',
        'description',
        'status',
        'main_total',
        'sub_total',
        'labor_total',
        'total',
        'is_locked',
        'converted_at',
    ];

    protected $casts = [
        'main_total'   => 'decimal:2',
        'sub_total'    => 'decimal:2',
        'labor_total'  => 'decimal:2',
        'total'        => 'decimal:2',
        'is_locked'    => 'boolean',
        'converted_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(Employee::class, 'creator_id');
    }

    public function articleGroup()
    {
        return $this->belongsTo(ArticleGroup::class, 'article_group_id');
    }

    public function targetMasterSet()
    {
        return $this->belongsTo(MasterSet::class, 'target_master_set_id');
    }

    public function sections()
    {
        return $this->hasMany(MasterSetCartSection::class, 'cart_id')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function items()
    {
        return $this->hasMany(MasterSetCartItem::class, 'cart_id')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function rootItems()
    {
        return $this->hasMany(MasterSetCartItem::class, 'cart_id')
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('id');
    }
}