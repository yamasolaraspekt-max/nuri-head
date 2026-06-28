<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerMeterCabinet extends Model
{
    use HasFactory;

    protected $fillable = [
        'meter_cabinet', 
        'cabinet_size',
        'meter_cabinet_company', 
        'customer_id', 
        'postcode', 
        'meter_cabinet_company', 
        'wp_meter_adapter_plate',
        'wp_ac_surge_protection',
        'wp_ac_switch',
        'wp_apz_field',
        'wp_disconnect_relay',
        'wp_equipotential_bonding',
    ];

}
