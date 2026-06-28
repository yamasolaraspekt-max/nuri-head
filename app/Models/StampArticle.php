<?php

 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StampArticle extends Model
{
    protected $fillable = [
        'product_id',
        'employee_id',
        'type',
        'label',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
