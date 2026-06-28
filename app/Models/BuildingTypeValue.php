<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuildingTypeValue extends Model
{
    use HasFactory;

    public $fillable = [
        'building_type_id',
        'size', 
        'value'
    ];

       public function buildingType()
    {
        return $this->belongsTo(BuildingType::class, 'building_type_id');
    }
}
