<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'customer_id',
        'alternative_id',
        'product_id',
        'employee_id',
        'ticket_type',
        'image_name',
        'image',
        'file_type',
        'status',
    ];
}
