<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PVLongRoof extends Model
{
    use HasFactory;

     protected $fillable = [
        'product_id',
        'roof_id',
        'roof_dimensions',
        'rafter_left_overhang',
        'roof_width',
        'roof_height',
        'rafter_right_overhang',
        'rafter_thickness',
        'rafter_reinforcement_needed',
        'statics_available',
        'conduit_available',
        'cable_routing_through',
        'lightning_protection',
        'dachsanierung',
        'geplante_termin',
        'dachdecker',
        'dauer',
        'ort',
        'solarhalteziegel',
        'ansprechpartner',
        'geliefert_durch',
        'geruestnutzung'
    ];
}
