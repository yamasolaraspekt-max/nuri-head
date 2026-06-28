<?php

 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProductFavoriteList extends Model
{
    protected $fillable = [
        'employee_id',
        'name',
        'slug',
        'color',
        'icon',
        'is_shared',
        'description',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->slug) {
                $model->slug = Str::slug($model->name) . '-' . Str::random(6);
            }
        });
    }

    public function owner()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function items()
    {
        return $this->hasMany(ProductFavoriteListItem::class, 'product_favorite_list_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_favorite_list_items', 'product_favorite_list_id', 'product_id')
            ->withPivot(['employee_id', 'note'])
            ->withTimestamps();
    }
}
