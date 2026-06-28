<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferDeleteLog extends Model
{
    protected $fillable = [
        'offer_id',
        'offer_no',
        'user_id',
        'employee_id',
        'delete_type',
        'reason',
        'snapshot',
        'deleted_at',
    ];

    protected $casts = [
        'snapshot' => 'array',
        'deleted_at' => 'datetime',
    ];
}