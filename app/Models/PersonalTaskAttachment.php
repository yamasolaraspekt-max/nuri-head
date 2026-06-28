<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PersonalTaskAttachment extends Model
{
    use SoftDeletes;

    protected $table = 'personal_task_attachments';

    protected $fillable = [
        'task_id',
        'customer_id',
        'image_name',
        'image',
        'file_type',
    ];

    public function task()
    {
        return $this->belongsTo(PersonalTask::class, 'task_id');
    }

    public function customer()
    {
        return $this->belongsTo(NewLeads::class, 'customer_id');
    }
}