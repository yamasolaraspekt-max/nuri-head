<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KanbanLeadTask extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'lead_product_list_id',
        'customer_id',
        'alternative_id',
        'product_id',
        'lead_stage_id',
        'lead_sub_stage_id',
        'task_phase_id',
        'phase_activity_id',
        'title',
        'description',
        'internal_note',
        'is_manual',
        'is_scheduled',
        'photo_required',
        'status',
        'estimated_minutes',
        'planned_start_at',
        'planned_end_at',
        'done_at',
        'created_by_employee_id',
        'performer_employee_id',
        'scheduled_by_employee_id',
        'done_by_employee_id',
        'meta',
    ];

    protected $casts = [
        'is_manual' => 'boolean',
        'is_scheduled' => 'boolean',
        'photo_required' => 'boolean',
        'estimated_minutes' => 'integer',
        'planned_start_at' => 'datetime',
        'planned_end_at' => 'datetime',
        'done_at' => 'datetime',
        'meta' => 'array',
    ];

    public function leadProduct()
    {
        return $this->belongsTo(LeadProductList::class, 'lead_product_list_id');
    }

    public function customer()
    {
        return $this->belongsTo(NewLeads::class, 'customer_id');
    }

    public function alternative()
    {
        return $this->belongsTo(LeadAlternativeAdd::class, 'alternative_id');
    }

    public function product()
    {
        return $this->belongsTo(ArticleGroup::class, 'product_id');
    }

    public function leadStage()
    {
        return $this->belongsTo(LeadStage::class, 'lead_stage_id');
    }

    public function leadSubStage()
    {
        return $this->belongsTo(LeadStageSubStage::class, 'lead_sub_stage_id');
    }

    public function taskPhase()
    {
        return $this->belongsTo(TaskPhase::class, 'task_phase_id');
    }

    public function phaseActivity()
    {
        return $this->belongsTo(PhaseActivities::class, 'phase_activity_id');
    }

    public function creator()
    {
        return $this->belongsTo(Employee::class, 'created_by_employee_id');
    }

    public function performer()
    {
        return $this->belongsTo(Employee::class, 'performer_employee_id');
    }

    public function scheduledBy()
    {
        return $this->belongsTo(Employee::class, 'scheduled_by_employee_id');
    }

    public function doneBy()
    {
        return $this->belongsTo(Employee::class, 'done_by_employee_id');
    }

    public function employees()
    {
        return $this->belongsToMany(
            Employee::class,
            'kanban_lead_task_employees',
            'kanban_lead_task_id',
            'employee_id'
        )->withPivot([
                    'role',
                    'status',
                    'assigned_at',
                    'done_at',
                ])->withTimestamps();
    }

    public function getIsOverdueAttribute(): bool
    {
        if ($this->status === 'done') {
            return false;
        }

        return $this->planned_end_at && $this->planned_end_at->isPast();
    }
}