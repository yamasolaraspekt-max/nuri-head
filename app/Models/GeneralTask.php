<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GeneralTask extends Model
{
    use SoftDeletes;

    protected $table = 'general_tasks';

    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'visibility',
        'department_id',
        'created_by',
        'claimed_by',
        'due_at',
        'planned_hours_today',
        'completed_at',
        'archived_at',
        'sort_order',

        // Recurring tasks
        'is_recurring',
        'recurrence_frequency',
        'recurrence_weekday',
        'recurrence_ends_at',
        'recurrence_parent_id',
        'recurrence_generated_from_id',
        'show_due_datetime',

        // Step / bulk task workflow
        'task_mode',
        'progress_percent',
        'soll_minutes',
        'ist_minutes',
    ];

    protected $casts = [
        'department_id' => 'integer',
        'created_by' => 'integer',
        'claimed_by' => 'integer',
        'due_at' => 'datetime',
        'completed_at' => 'datetime',
        'archived_at' => 'datetime',
        'planned_hours_today' => 'decimal:2',
        'sort_order' => 'integer',

        'is_recurring' => 'boolean',
        'recurrence_weekday' => 'integer',
        'recurrence_ends_at' => 'datetime',
        'recurrence_parent_id' => 'integer',
        'recurrence_generated_from_id' => 'integer',
        'show_due_datetime' => 'boolean',

        'progress_percent' => 'integer',
        'soll_minutes' => 'integer',
        'ist_minutes' => 'integer',
    ];

    protected $attributes = [
        'status' => 'open',
        'priority' => 'normal',
        'visibility' => 'all',
        'sort_order' => 0,
        'is_recurring' => false,
        'show_due_datetime' => true,
        'task_mode' => 'single',
        'progress_percent' => 0,
        'soll_minutes' => 0,
        'ist_minutes' => 0,
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->creator();
    }

    public function claimedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'claimed_by');
    }

    public function assignees(): BelongsToMany
    {
        return $this->belongsToMany(
            Employee::class,
            'general_task_assignees',
            'general_task_id',
            'employee_id'
        )->withTimestamps();
    }

    public function reports(): HasMany
    {
        return $this->hasMany(GeneralTaskReport::class, 'general_task_id')->latest();
    }

    public function steps(): HasMany
    {
        return $this->hasMany(GeneralTaskStep::class, 'general_task_id')->orderBy('sort_order');
    }

    /**
     * Parent/predecessor tasks. These tasks must be completed before this task.
     * Pivot table columns: task_id = current child task, depends_on_task_id = parent task.
     */
    public function dependsOn(): BelongsToMany
    {
        return $this->belongsToMany(
            self::class,
            'general_task_dependencies',
            'task_id',
            'depends_on_task_id'
        )->withPivot(['type', 'lag_days', 'created_by'])->withTimestamps();
    }

    /**
     * Child/dependent tasks that are blocked by this task.
     */
    public function blockingTasks(): BelongsToMany
    {
        return $this->belongsToMany(
            self::class,
            'general_task_dependencies',
            'depends_on_task_id',
            'task_id'
        )->withPivot(['type', 'lag_days', 'created_by'])->withTimestamps();
    }

    // Aliases used by some Blade/controller versions.
    public function dependencyParents(): BelongsToMany
    {
        return $this->dependsOn();
    }

    public function dependencyChildren(): BelongsToMany
    {
        return $this->blockingTasks();
    }

    public function recurrenceParent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'recurrence_parent_id');
    }

    public function generatedFrom(): BelongsTo
    {
        return $this->belongsTo(self::class, 'recurrence_generated_from_id');
    }

    public function generatedTasks(): HasMany
    {
        return $this->hasMany(self::class, 'recurrence_generated_from_id');
    }

    public function getIsArchivedAttribute(): bool
    {
        return $this->trashed() || $this->status === 'archived' || !empty($this->archived_at);
    }

    public function getDoneStepsCountAttribute(): int
    {
        if ($this->relationLoaded('steps')) {
            return $this->steps->where('is_done', true)->count();
        }

        return $this->steps()->where('is_done', true)->count();
    }

    public function getStepsCountAttribute(): int
    {
        if ($this->relationLoaded('steps')) {
            return $this->steps->count();
        }

        return $this->steps()->count();
    }
}
