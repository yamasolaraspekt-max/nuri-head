<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuildingType extends Model
{
    use HasFactory;

    public $fillable = [
        'building_type',
          'status',
           'start_year',
           'end_year', 
    ];
}
