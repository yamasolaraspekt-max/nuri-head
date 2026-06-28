<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchContractDetails extends Model
{
    use HasFactory;

    public $fillable=[
       'rent_properties_id',
        'position',
        'name',
        'email',
        'phone',
        'home',
        'office',
        'address',
        'status',
    ];
}
