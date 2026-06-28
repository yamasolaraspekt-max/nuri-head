<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeneralTaskReport extends Model
{
    protected $table = 'general_task_reports';

    protected $fillable = [
        'general_task_id',
        'employee_id',
        'type',
        'body',
        'hours',
    ];

    protected $casts = [
        'general_task_id' => 'integer',
        'employee_id' => 'integer',
        'hours' => 'decimal:2',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(GeneralTask::class, 'general_task_id');
    }

    public function generalTask(): BelongsTo
    {
        return $this->task();
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
