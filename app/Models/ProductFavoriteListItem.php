<?php

 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductFavoriteListItem extends Model
{
    protected $fillable = [
        'product_favorite_list_id',
        'product_id',
        'employee_id',
        'note',
    ];

    public function list()
    {
        return $this->belongsTo(ProductFavoriteList::class, 'product_favorite_list_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
