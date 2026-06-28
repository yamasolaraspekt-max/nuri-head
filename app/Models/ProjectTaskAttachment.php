<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

class ProjectTaskAttachment extends Model
{
    //
           use SoftDeletes, Notifiable, HasFactory;

        protected $fillable = [
        'customer_id',
        'project_id',
        'phase_id',
        'activity_id',
        'upload_by',
        'image_name',
        'image',
        'file_type'
    ];

   public function uploader()
    {
        return $this->belongsTo(Employee::class, 'upload_by'); // adjust as needed
    }

}
