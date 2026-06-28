<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BatterySystem extends Model
{
    use HasFactory;

     protected $fillable = [
        'product_id', 
        'article_group_id',
        'ess_company', 
        'ess_name', 
        'ess_description', 
        'ess_available', 
        'ess_version', 
        'ess_user_id',
        'nominal_power',
        'max_charge_power',
        'max_discharge_power',
        'coupling_type',
        'ess_efficiency_0',
        'ess_efficiency_5',
        'ess_efficiency_10',
        'ess_efficiency_20',
        'ess_efficiency_30',
        'ess_efficiency_50',
        'ess_efficiency_75',
        'ess_efficiency_100',
        'ess_equalization_charge',
        'ess_equalization_charge_end',
        'ess_equalization_charge_duration',
        'ess_equalization_charge_cycle',
        'ess_full_charge',
        'ess_full_charge_end',
        'ess_full_charge_duration',
        'ess_full_charge_cycle',
        'ess_maintenance_charge',
        'ess_uo_charge',
        'ess_uo_charge_end',
        'ess_uo_charge_duration',
        'ess_i_charge',
        'ess_i_charge_end',
        'ess_battery',
        'ess_num_batteries_per_string',
        'ess_num_battery_strings',
        'ess_system_voltage',
        'ess_usable_energy',
        'ess_capacity_c10'
    ];
}
