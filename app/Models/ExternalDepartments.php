<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalDepartments extends Model
{
    use HasFactory;
     protected $fillable=[
        'external_id', 'department', 'email', 'phone', 'status', 'name', 'position', 'home', 'office'
    ];
}
