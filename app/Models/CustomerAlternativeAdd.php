<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerAlternativeAdd extends Model
{
    use HasFactory;

    public $fillable = [
        'customer_id',
        'street',
        'postcode',
        'city',
         'main', 
         'lat', 'lon', 'elevation'
         ,'address_no'
    ];
}

