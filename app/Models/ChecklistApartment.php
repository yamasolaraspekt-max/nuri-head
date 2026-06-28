<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistApartment extends Model
{
    use HasFactory;

   protected $fillable = [
        'story', 'unit', 'living_space','customer_id', 'address_no','heating_living_space' , 'usable_space' 
    ];
}
