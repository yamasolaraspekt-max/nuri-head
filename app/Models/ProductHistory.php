<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductHistory extends Model
{
    protected $fillable = [
        'product_id',
        'changed_by',
        'action',
        'changed_fields',
        'old_values',
        'new_values',
    ];

    protected $casts = [
        'changed_fields' => 'array',
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'changed_by');
    }
}