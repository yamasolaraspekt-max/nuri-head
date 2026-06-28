<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterSetCartSection extends Model
{
    protected $fillable = [
        'cart_id',
        'name',
        'color',
        'sort_order',
        'is_collapsed',
    ];

    protected $casts = [
        'is_collapsed' => 'boolean',
    ];

    public function cart()
    {
        return $this->belongsTo(MasterSetCart::class, 'cart_id');
    }

    public function items()
    {
        return $this->hasMany(MasterSetCartItem::class, 'section_id')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function rootItems()
    {
        return $this->hasMany(MasterSetCartItem::class, 'section_id')
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('id');
    }
}