<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KlimaPlz extends Model
{
    protected $table = 'klima_plz';

    protected $guarded = [];

    protected $casts = [
        'lat' => 'decimal:5',
        'lon' => 'decimal:5',
        'nat_c' => 'decimal:2',
        'temp_mittel_c' => 'decimal:4',
        'hgt15_kd' => 'integer',
        'vollbenutz_h' => 'decimal:2',
        'hoehe_m' => 'integer',
    ];
}
