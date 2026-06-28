<?php

namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeDocument extends Model
{
     use HasFactory, softDeletes;

    public $fillable = [
        'employee_id', 
        'created_by',  
        'image_name',  
        'image',
        'file_type',
        'type',
        'status', 
        'updated_by',
        'deleted_at'
    ];
}
