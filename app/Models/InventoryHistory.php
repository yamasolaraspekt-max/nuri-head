<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryHistory extends Model
{
    protected $fillable = [
        'inventory_id',
        'product_id',
        'customer_id',
        'used_by',
        'type',
        'quantity_before',
        'quantity_used',
        'quantity_after',
        'usage_location',
        'note',
        'used_at',
    ];

    protected $casts = [
        'quantity_before' => 'decimal:2',
        'quantity_used' => 'decimal:2',
        'quantity_after' => 'decimal:2',
        'used_at' => 'datetime',
    ];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function customer()
    {
        return $this->belongsTo(\App\Models\NewLeads::class, 'customer_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'used_by');
    }
}