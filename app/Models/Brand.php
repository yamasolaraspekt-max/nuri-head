<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'address', 'status', 'type'
    ];

    public function products()
{
    return $this->hasMany(Product::class);
}

public function departments()
    {
        return $this->hasMany(BrandDepartment::class, 'brand_id');
    }


    public function offerTemplates()
    {
        return $this->hasMany(\App\Models\OfferTemplate::class, 'brand_id');
    }

}
