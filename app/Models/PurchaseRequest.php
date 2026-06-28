<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'brand',
        'new_brand',
        'distributor_id',
        'new_distributor',
        'product',
        'model',
        'color',
        'request_from',
        'request_to',
        'measure_unit',
        'price_unit',
        'retail_price',
        'retail_discount_type',
        'retail_discount',
        'purchase_price',
        'short_description',
        'used',
        'customer_id',
        'employee_id',
        'problem_id',
        'link',
        'image',
        'quantity',
        'status',
        'add_by',
        'add_date',
        'edit_by',
        'edit_date',
        'delete_by',
        'delete_date',
    ];

    protected $casts = [
        'brand' => 'integer',
        'distributor_id' => 'integer',
        'request_from' => 'integer',
        'request_to' => 'integer',
        'customer_id' => 'integer',
        'employee_id' => 'integer',
        'problem_id' => 'integer',
        'quantity' => 'decimal:2',
        'retail_price' => 'decimal:2',
        'purchase_price' => 'decimal:2',
        'retail_discount' => 'decimal:2',
        'add_date' => 'datetime',
        'edit_date' => 'datetime',
        'delete_date' => 'datetime',
    ];

    public function brandData()
    {
        return $this->belongsTo(Brand::class, 'brand');
    }

    public function distributor()
    {
        return $this->belongsTo(Distributor::class, 'distributor_id');
    }

    public function requestFrom()
    {
        return $this->belongsTo(Employee::class, 'request_from');
    }

    public function requestTo()
    {
        return $this->belongsTo(Employee::class, 'request_to');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}