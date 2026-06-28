<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskSubTask extends Model
{
    use HasFactory;

    public $fillable = [
        'task_id',
        'phase_id',
        'task_title',
        'description',
        'duration', 
        'duration_type',
        'status', 
        'photo',
        'image',
        'answered_by'
        
    ];

    public function phase()
    {
        return $this->belongsTo(TaskPhase::class, 'phase_id');
    }

    public function task()
    {
        return $this->belongsTo(PhaseActivity::class, 'task_id');
    }

}
