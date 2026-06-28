<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentProperty extends Model
{
    use HasFactory;

    public $fillable=[
       'object_id',
        'owner' , 
        'living_space' ,
        'parking' ,
        'parking_cost',
        'parking_count' ,
        'contract_date' ,
        'contract_type' ,
        'termination_date',
        'termination_type',
        'cold_rent',
        'extra_cost' ,
        'advance_rent' ,
        'bank_user' ,
        'bank_name' ,
        'iban',
        'status',
    ];

   

}
