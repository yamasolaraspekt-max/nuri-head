<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierConnectionMapping extends Model
{
    protected $fillable = [
        'supplier_connection_id',
        'source_field',
        'target_table',
        'target_field',
        'transformer',
        'default_value',
        'is_required',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function supplierConnection()
    {
        return $this->belongsTo(SupplierConnection::class);
    }
}