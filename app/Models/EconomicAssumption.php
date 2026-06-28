<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EconomicAssumption extends Model
{
    protected $fillable = [
        'electricity_price', 'gas_price', 'price_increase_rate', 'inflation_rate', 'lifespan'
    ];
}
