<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HandoverTo extends Model
{
    use HasFactory;

    protected $fillable=['handover_id', 'handover_to', 'handover_from', 'handover_by', 'handover_date', 'purpose', 'status'];
}
