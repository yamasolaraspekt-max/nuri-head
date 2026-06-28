<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerHeatingCircuit extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id' ,'heating_circuit_number', 'flow_temperature', 
        'return_flow_temperature', 'room_story', 'pipe_dimension', 'pipe_material'
    ];
}
