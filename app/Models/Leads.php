<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Leads extends Model
{
    use HasFactory;

    protected $fillable=[
        'subject', 'body', 'attachments_count', 'sender_name', 'sender_email', 'recipient_name', 'status'
    ];
}
