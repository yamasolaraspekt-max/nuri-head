<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentExtraCost extends Model
{
    use HasFactory;

    public $fillable = [
        'branch_rent_infos_id',
        'title',
        'cost', 
        'paid_to',
        'company', 
        'status' 
    ];
}
