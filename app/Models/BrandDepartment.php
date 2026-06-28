<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrandDepartment extends Model
{
    use HasFactory;

    protected $fillable=[
        'brand_id', 'brand_department', 'email', 'phone', 'status', 'name', 'position', 'home', 'office'
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
}
