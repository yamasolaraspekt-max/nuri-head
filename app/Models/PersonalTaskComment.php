<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PersonalTaskComment extends Model
{
    use SoftDeletes;

    protected $table = 'personal_task_comments';

    protected $fillable = [
        'task_id',
        'comment_by',
        'comment',
        'status',
        'parent_id',
    ];

    public function task()
    {
        return $this->belongsTo(PersonalTask::class, 'task_id');
    }

    public function author()
    {
        return $this->belongsTo(Employee::class, 'comment_by');
    }

    public function parent()
    {
        return $this->belongsTo(PersonalTaskComment::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(PersonalTaskComment::class, 'parent_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'comment_by');
    }
 

    public function replies()
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}
