<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskToDo extends Model
{
    use HasFactory;

    protected $fillable = [
            'customer_id',
            'alternative',
            'phase_id',
            'product_id',
            'activities_id',
            'sub_task_id',
            'project_id',
            'contact_person',
            'responsible_person', 
            'outside_service',
            'outside_company',
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
        ];
    public function customer()
    {
        return $this->belongsTo(NewLead::class, 'customer_id');
    }

    public function phase()
    {
        return $this->belongsTo(TaskPhase::class, 'phase_id');
    }

    public function activity()
    {
        return $this->belongsTo(PhaseActivity::class, 'activities_id');
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

    public function product()
    {
        return $this->belongsTo(ArticleGroup::class, 'product_id');
    }

    public function subTask()
    {
        return $this->belongsTo(TaskSubTask::class, 'sub_task_id');
    }

    public function alternative()
    {
        return $this->belongsTo(LeadAlternativeAdd::class, 'alternative');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }


    public function labels()
    {
        return $this->hasMany(TaskLabel::class, 'task_id');
    }
}
