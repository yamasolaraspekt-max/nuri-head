<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Battery extends Model
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
        'battery_type',
        'cell_voltage',
        'num_cells',
        'nominal_voltage',
        'num_strings',
        'internal_resistance',
        'self_discharge',
        'dod_20',
        'dod_40',
        'dod_60',
        'dod_80',
        'capacity_10min',
        'capacity_30min',
        'capacity_1h',
        'capacity_5h',
        'capacity_10h',
        'capacity_100h',
        'length',
        'width',
        'height',
        'weight'
    ];
}
