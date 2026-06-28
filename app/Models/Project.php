<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;


class Project extends Model
{
     use SoftDeletes, Notifiable, HasFactory; 

    protected $fillable = [
      'customer_id',
      'product_id',
      'alternative_id',
      'service_id',
      'department_id',
      'service',
      'employee_id',
      'project_leader',
      'progress',
      'project_start',
      'montage_start',
      'end_date',
      'start_time',
      'end_time',
      'total_time',
      'color',
      'project_status',
      'priority',
      'status',
      'status_msg'
  ];

    // 🔗 Relationships

    public function customer()
    {
        return $this->belongsTo(NewLeads::class, 'customer_id');
    }

    public function product()
    {
        return $this->belongsTo(ArticleGroup::class, 'product_id');
    }

    public function alternative()
    {
        return $this->belongsTo(LeadAlternativeAdd::class, 'alternative_id');
    }

    public function serviceSection()
    {
        return $this->belongsTo(PhaseSection::class, 'service_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function assignedEmployee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function leader()
    {
        return $this->belongsTo(Employee::class, 'project_leader');
    }


    public function feedbacks()
    {
        return $this->hasMany(ProjectFeedback::class);
    }

    public function award()
    {
        return $this->hasOne(ProjectAward::class);
    }

    public function project_timelines()
{
    return $this->hasMany(ProjectTimeline::class);
}


public function members()
{
    return $this->hasMany(AddEmployeeToProject::class);
}
}
