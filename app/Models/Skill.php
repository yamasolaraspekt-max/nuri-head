<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;

    protected $fillable=[
        'product_id',
        'emp_id',
        'advice',
        'plan',
        'calculation',
        'montage',
        'project_planing',
        'site_management',
    ];
}
