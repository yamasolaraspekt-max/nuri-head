<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Image extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'article_group',
        'alternative_id',
        'phase_id',
        'task_id',
        'sub_task_id',
        'created_by',
        'update_by',
        'stage',
        'image_name',
        'image',
        'file_type',
        'status',
    ];
}