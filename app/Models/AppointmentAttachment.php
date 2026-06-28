<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class AppointmentAttachment extends Model
{
       use HasFactory, SoftDeletes;


    protected $fillable = [
        'appointment_id',  
        'image_name', 
        'image', 
        'file_type'
    ];
}
