<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPV extends Model
{
    use HasFactory;
 
   protected $fillable = [
        'product_id',
        'article_group_id',

        'cell_type',
        'half_cell_module',
        'num_cells',
        'num_bypass_diodes',
        'voltage_loss_per_bypass_diode',
        'integrated_power_optimizer',
        'trafo_inverter_only',
        'cell_strands_vertical',
        'mpp_voltage',
        'mpp_current',
        'open_circuit_voltage',
        'short_circuit_current',
        'voltage_increase_before_stabilization',
        'nominal_power',
        'fill_factor',
        'efficiency',
        'low_light_model',
        'irradiance',
        'mpp_voltage_low_light',
        'mpp_current_low_light',
        'open_circuit_voltage_low_light',
        'short_circuit_current_low_light',
        'fill_factor_low_light',
        'efficiency_low_light',
        'standard_low_light_behavior',
        'temperature_coefficient_voc',
        'temperature_coefficient_voc_pct',
        'temperature_coefficient_isc',
        'temperature_coefficient_isc_pct',
        'temperature_coefficient_pmax',
        'angle_correction_factor',
        'max_system_voltage',
        'bifaciality_factor',
        'width',
        'height',
        'area',
        'depth',
        'frame_width',
        'weight',
        'status'
    ];

    

}
