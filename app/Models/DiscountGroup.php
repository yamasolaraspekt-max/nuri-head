<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountGroup extends Model
{
    use HasFactory;

    protected $fillable = ['discount_group', 'discount'];

}
