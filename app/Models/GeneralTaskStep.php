<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class GeneralTaskStep extends Model
{
    use SoftDeletes;

    protected $table = 'general_task_steps';

    protected $fillable = [
        'general_task_id',
        'title',
        'description',
        'sort_order',
        'soll_minutes',
        'ist_minutes',
        'is_done',
        'checked_by',
        'checked_at',
        'due_at',
        'created_by',
    ];

    protected $casts = [
        'general_task_id' => 'integer',
        'sort_order' => 'integer',
        'soll_minutes' => 'integer',
        'ist_minutes' => 'integer',
        'is_done' => 'boolean',
        'checked_by' => 'integer',
        'checked_at' => 'datetime',
        'due_at' => 'datetime',
        'created_by' => 'integer',
    ];

    protected $attributes = [
        'sort_order' => 1,
        'soll_minutes' => 0,
        'ist_minutes' => 0,
        'is_done' => false,
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(GeneralTask::class, 'general_task_id');
    }

    public function assignees(): BelongsToMany
    {
        return $this->belongsToMany(
            Employee::class,
            'general_task_step_assignees',
            'general_task_step_id',
            'employee_id'
        )->withTimestamps();
    }

    public function checkedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'checked_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->creator();
    }

    public function getSollHoursAttribute(): float
    {
        return round(((int) $this->soll_minutes) / 60, 2);
    }

    public function getIstHoursAttribute(): float
    {
        return round(((int) $this->ist_minutes) / 60, 2);
    }


    public function getIsOverdueAttribute(): bool
    {
        return !empty($this->due_at)
            && empty($this->is_done)
            && $this->due_at->isPast();
    }
}
