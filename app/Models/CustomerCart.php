<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product; // Replace with the actual namespace of your Product model

class CustomerCart extends Model
{
    use HasFactory;

    public function product()
    {
        return $this->belongsToMany(Product::class);
    }

}
