<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BatteryInverter extends Model
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
        'nominal_voltage',
        'max_ac_current',
        'continuous_power',
        'power_30min',
        'power_60min',
        'no_load_consumption',
        'standby_consumption',
        'battery_voltage',
        'min_battery_voltage',
        'max_battery_voltage',
        'max_battery_charge_current',
        'efficiency_0',
        'efficiency_5',
        'efficiency_10',
        'efficiency_20',
        'efficiency_30',
        'efficiency_50',
        'efficiency_75',
        'efficiency_100',
        'max_devices_per_phase_single',
        'max_devices_per_phase_dual',
        'max_clusters',
    ]; 
}
