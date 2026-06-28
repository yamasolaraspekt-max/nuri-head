<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
   protected $fillable = [
        'product_id',
        'serial_no',
        'article_no',
        'ean',
        'manual_no',
        'location',
        'location_label',
        'latitude',
        'longitude',
        'inventory_category',
        'room_name',
        'room_number',
        'rack_name',
        'row',
        'column',
        'shelf',
        'quantity',
        'responsible_id',
        'add_by',
        'add_date',
    ];

    protected $casts = [
        'quantity'   => 'float',
        'latitude'   => 'float',
        'longitude'  => 'float',
        'add_date'   => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function responsible()
    {
        return $this->belongsTo(Employee::class, 'responsible_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'location');
    }
 
    public function history()
    {
        return $this->hasMany(\App\Models\InventoryHistory::class, 'inventory_id');
    }
}