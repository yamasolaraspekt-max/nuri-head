<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PowerOptimizer extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'article_group_id',
        'company',
        'name',
        'description',
        'available',
        'version', 
        'user_id',
        'ac_nominal_voltage',
        'ac_nominal_current',
        'ac_nominal_power',
        'max_ac_power',
        'num_phases',
        'load_0',
        'load_25',
        'load_50',
        'load_75',
        'load_100',
    ];

}
