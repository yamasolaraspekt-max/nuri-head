<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectTask extends Model
{
    protected $fillable = [
        'project_id',
        'customer_id',
        'checklist_id',
        'phase_id',
        'activities_id',
        'product_id',
        'service_id',
        'department_id',
        'service',
        'alternative_id',
        'parent_id',
        'contact_person',
        'responsible_person',
        'outside_service',
        'outside_company',
        'color',
        'active_by',
        'jump_steps',
        'jump_steps_by',
        'done',
        'type',
        'main_id',
        'outside_type',
        'done_date',
        'reason',
        'done_status',
        'status',
        'work_progress',
        'more_time',
        'total_time',
        'start_date',
        'due_date',
        'verify',
        'verify_by'
    ];

    // 🔗 Relationships

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function customer()
    {
        return $this->belongsTo(NewLead::class, 'customer_id');
    }

    public function checklist()
    {
        return $this->belongsTo(ProjectMontageChecklist::class, 'checklist_id');
    }

    public function phase()
    {
        return $this->belongsTo(TaskPhase::class, 'phase_id');
    }

    public function activity()
    {
        return $this->belongsTo(PhaseActivity::class, 'activities_id');
    }

    public function product()
    {
        return $this->belongsTo(ArticleGroup::class, 'product_id');
    }

    public function serviceSection()
    {
        return $this->belongsTo(PhaseSection::class, 'service_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function alternative()
    {
        return $this->belongsTo(LeadAlternativeAdd::class, 'alternative_id');
    }

    public function contactPerson()
    {
        return $this->belongsTo(Employee::class, 'contact_person');
    }

    public function responsiblePerson()
    {
        return $this->belongsTo(Employee::class, 'responsible_person');
    }

    public function outsideService()
    {
        return $this->belongsTo(Employee::class, 'outside_service');
    }

    public function outsideCompany()
    {
        return $this->belongsTo(ExternalPersonal::class, 'outside_company');
    }

    public function parent()
    {
        return $this->belongsTo(ProjectTask::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ProjectTask::class, 'parent_id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verify_by');
    }
}
