<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inverter extends Model
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
        'created_at',
        'user_id',
        'dc_nominal_power',
        'max_dc_power',
        'dc_nominal_voltage',
        'max_input_voltage',
        'max_input_current',
        'max_dc_short_circuit_current',
        'num_dc_inputs',
        'ac_nominal_power',
        'max_ac_power',
        'ac_nominal_voltage',
        'num_phases',
        'with_transformer',
        'efficiency_change',
        'min_feed_power',
        'standby_consumption',
        'night_consumption',
        'power_range_below_20',
        'power_range_above_20',
        'parallel_operation',
        'num_mpp_trackers',
        'identical_properties',
        'max_input_current_per_mpp',
        'max_short_circuit_current_per_mpp',
        'max_input_power_per_mpp',
        'min_mpp_voltage',
        'max_mpp_voltage',
        'efficiency_0',
        'efficiency_5',
        'efficiency_10',
        'efficiency_20',
        'efficiency_30',
        'efficiency_50',
        'efficiency_75',
        'efficiency_100',
    ];
}
