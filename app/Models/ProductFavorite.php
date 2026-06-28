<?php

// app/Models/ProductFavorite.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductFavorite extends Model
{
    protected $fillable = [
        'employee_id',
        'product_id',
        'note',
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
