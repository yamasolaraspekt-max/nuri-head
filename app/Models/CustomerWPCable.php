<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerWPCable extends Model
{
    use HasFactory;

   protected $fillable = [
        'customer_id',
        'postcode',
        'system',
        'type',
        'dimension',
        'company',
        'designation',
        'note',
    ];
}
