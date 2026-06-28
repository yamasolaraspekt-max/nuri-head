<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryRequestOut extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'responsible_id',
        'requester_id',
        'reason',
        'quantity',
        'add_by',
        'add_date',
        'delete_by',
        'delete_date',
        'edit_by',
        'edit_date',
        'status',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'add_date' => 'date',
        'delete_date' => 'date',
        'edit_date' => 'date',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function responsible()
    {
        return $this->belongsTo(Employee::class, 'responsible_id');
    }

    public function requester()
    {
        return $this->belongsTo(Employee::class, 'requester_id');
    }
}