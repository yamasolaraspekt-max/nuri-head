<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\AuditableLead;
class CustomerRoomDimension extends Model
{

    use HasFactory;
    use AuditableLead;
     protected $fillable = [
        'customer_id',
        'room_id', 
        'room_number',
        'dimension_type',
        'width',
        'height',
        'ceiling_height',
        'stair_form',
        'stair_width',
        'room_story'
    ];
}
