<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Handover extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'handover_id',
        'quantity',
        'remark',
        'status',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];
}
