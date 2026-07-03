<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

class PersonalTask extends Model
{
    use SoftDeletes, Notifiable, HasFactory;

    protected $table = 'personal_tasks';

    protected $fillable = [
        'task_id',
        'deal_measurement_id',
        'task_title',
        'description',
        'assigned_by',
        'task_status',
        'board_column',
        'priority',
        'color',
        'repeat',
        'reminder_date',
        'reminder_time',
        'progress',
        'public',
        'type',
        'due_date',
        'due_time',
        'total_day',
        'total_time',
        'start_date',
        'controller_id',
        'is_customer',
        'customer_id',
        'alternative_id',
        'product_id',
        'is_report',
        'archived_at',
        'next_reminder_at',
        'last_reminded_at',
        'reminder_count',
        'next_repeat_at',
        'last_repeated_at',
        'repeat_parent_id',
        'lead_product_list_id',
        'lead_stage_id',
        'lead_stage_sub_stage_id',
        'follow_up_art',
        'source_type',
        'source_id',
    ];

    protected $casts = [
        'controller_id' => 'array',
        'is_customer' => 'boolean',
        'public' => 'boolean',
        'is_report' => 'boolean',
        'start_date' => 'date',
        'due_date' => 'date',
        'archived_at' => 'datetime',
        'deleted_at' => 'datetime',
        'next_reminder_at' => 'datetime',
        'last_reminded_at' => 'datetime',
        'next_repeat_at' => 'datetime',
        'last_repeated_at' => 'datetime',
        'reminder_count' => 'integer',
        'progress' => 'integer',
        'deal_measurement_id' => 'integer',
        'lead_product_list_id' => 'integer',
        'lead_stage_id' => 'integer',
        'lead_stage_sub_stage_id' => 'integer',
    ];

    public const BOARD_COLUMNS = [
        'overdue',
        'today',
        'this_week',
        'this_month',
        'later',
        'no_due',
    ];

    public function assignedUsers(): Collection
    {
        $employeeIds = $this->employees()
            ->pluck('employees.id')
            ->map(fn($id) => (string) $id)
            ->filter()
            ->values()
            ->all();

        if (empty($employeeIds)) {
            return collect();
        }

        return User::query()
            ->whereIn('name', $employeeIds)
            ->get();
    }

    public function getControllersAttribute(): Collection
    {
        $raw = $this->attributes['controller_id'] ?? null;

        if (empty($raw)) {
            return collect();
        }

        if (is_array($raw)) {
            $ids = $raw;
        } else {
            $decoded = json_decode($raw, true);
            $ids = is_array($decoded) ? $decoded : [$raw];
        }

        $ids = collect($ids)
            ->flatten()
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($ids)) {
            return collect();
        }

        return Employee::query()
            ->whereIn('id', $ids)
            ->get(['id', 'name', 'lastname', 'image']);
    }

    public function getEffectiveBoardColumnAttribute(): string
    {
        if (!empty($this->board_column) && in_array($this->board_column, self::BOARD_COLUMNS, true)) {
            return $this->board_column;
        }

        if (!$this->due_date) {
            return 'no_due';
        }

        $today = now()->startOfDay();
        $due = $this->due_date->copy()->startOfDay();

        if ($due->lt($today)) {
            return 'overdue';
        }

        if ($due->isSameDay($today)) {
            return 'today';
        }

        if ($due->betweenIncluded($today->copy()->addDay(), $today->copy()->endOfWeek())) {
            return 'this_week';
        }

        if ($due->betweenIncluded($today->copy()->startOfDay(), $today->copy()->endOfMonth())) {
            return 'this_month';
        }

        return 'later';
    }

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employees_personal_tasks', 'task_id', 'employee_id')
            ->withPivot(['status', 'reason', 'note', 'change_date', 'changed_by', 'change_reason'])
            ->withTimestamps();
    }

    public function taskKeys()
    {
        return $this->hasMany(PersonalTaskKey::class, 'personal_task_id');
    }
    public function keys()
    {
        return $this->hasMany(PersonalTaskKey::class, 'personal_task_id');
    }
    public function steps()
    {
        return $this->hasMany(PersonalTaskKey::class, 'personal_task_id')->orderBy('id');
    }
    public function dailyReportTimes()
    {
        return $this->morphMany(DailyReportTime::class, 'reportable');
    }
    public function comments()
    {
        return $this->hasMany(PersonalTaskComment::class, 'task_id')->whereNull('parent_id')->where('status', 'Published')->latest();
    }
    public function rootComments()
    {
        return $this->hasMany(PersonalTaskComment::class, 'task_id')->whereNull('parent_id')->where('status', 'Published')->latest();
    }
    public function attachments()
    {
        return $this->hasMany(PersonalTaskAttachment::class, 'task_id');
    }
    public function customer()
    {
        return $this->belongsTo(NewLeads::class, 'customer_id');
    }
    public function assignedBy()
    {
        return $this->belongsTo(Employee::class, 'assigned_by');
    }
    public function changedBy()
    {
        return $this->belongsTo(Employee::class, 'changed_by');
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
    public function product()
    {
        return $this->belongsTo(ArticleGroup::class, 'product_id');
    }
    public function histories()
    {
        return $this->hasMany(PersonalTaskHistory::class, 'task_id')->orderByDesc('created_at');
    }
    public function repeatParent()
    {
        return $this->belongsTo(self::class, 'repeat_parent_id');
    }
    public function repeats()
    {
        return $this->hasMany(self::class, 'repeat_parent_id');
    }

    public function dealMeasurement()
    {
        return $this->belongsTo(DealMeasurement::class, 'deal_measurement_id');
    }

    public function measurement()
    {
        return $this->dealMeasurement();
    }

    public function scopeNotArchived($query)
    {
        return $query->whereNull('archived_at');
    }
    public function scopeArchived($query)
    {
        return $query->whereNotNull('archived_at');
    }
    public function scopeOpen($query)
    {
        return $query->whereNotIn('task_status', ['completed', 'pause', 'cancel', 'junk', 'rejected']);
    }
    public function scopeVisibleBoardTasks($query)
    {
        return $query->whereNull('archived_at')->whereNull('deleted_at')->whereNotIn('task_status', ['completed', 'pause', 'cancel', 'junk', 'rejected']);
    }
    public function alternative()
    {
        return $this->belongsTo(LeadAlternativeAdd::class, 'alternative_id');
    }

    public function leadProductList()
    {
        return $this->belongsTo(LeadProductList::class, 'lead_product_list_id');
    }

    public function leadStage()
    {
        return $this->belongsTo(LeadStage::class, 'lead_stage_id');
    }

    public function leadStageSubStage()
    {
        return $this->belongsTo(LeadStageSubStage::class, 'lead_stage_sub_stage_id');
    }
}
