<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElectricVehicle extends Model
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
        'range_wltp',
        'consumption',
        'battery_capacity',
        'discharge_power',
        'motor_power',
        'empty_weight',
        'max_speed',
        'payload',
        'seats',
        'charging_technology',
        'charging_power',
        'discharge_for_consumption',
    ];
}
