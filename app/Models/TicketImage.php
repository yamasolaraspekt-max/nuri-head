<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'stage',
        'image_name',
        'image',
        'file_type',
        'status',
    ];

    public function ticket()
    {
        return $this->belongsTo(Problem::class, 'ticket_id');
    }
}