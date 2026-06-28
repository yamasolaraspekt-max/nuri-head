<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DealNote extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'deal_id',
        'customer_id',
        'alternative_id',
        'product_id',
        'parent_id',
        'description',
        'created_by',
        'updated_by',
    ];

    public function deal()
    {
        return $this->belongsTo(Deal::class, 'deal_id');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('created_at', 'asc');
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\Employee::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(\App\Models\Employee::class, 'updated_by');
    }
}