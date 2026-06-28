<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PhaseActivities extends Model
{
    use HasFactory,SoftDeletes; 
    protected $fillable = [
            'phase_id',
            'product_id',
            'section_id',
            'parent_id', 
            'section_name',
            'initial',
            'title',
            'duration',
            'duration_type',
            'description',
            'notes',
            'status',
            'photo',
            'link',
            'priority',
            'percent',
            'usage_count',
            'rating',
            'answered_by',
            'sort_order',
            'copy_count',
            'lead_stage_id',
            'lead_sub_stage_id',
        ];
 
        public function section()
    {
        return $this->belongsTo(PhaseSection::class, 'section_id');
    }


    public function contactPerson()
    {
        return $this->belongsTo(Employee::class, 'contact_person');
    }

    public function responsible()
    {
        return $this->belongsTo(Employee::class, 'responsible_person');
    }

    public function insideService()
    {
        return $this->belongsTo(Employee::class, 'inside_service');
    }

    public function outsideService()
    {
        return $this->belongsTo(Employee::class, 'outside_service');
    }
     public function taskDocuments()
    {
        return $this->hasMany(TaskDocument::class, 'activities_id');
    }


    public function parent()
    {
        return $this->belongsTo(PhaseActivities::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(PhaseActivities::class, 'parent_id');
    }
    public function departments()
    {
        return $this->belongsToMany(Department::class, 'activity_departments', 'activity_id', 'department_id');
    }

    public function positions()
    {
        return $this->belongsToMany(Position::class, 'activity_positions', 'activity_id', 'position_id');
    }

    public function articles()
    {
        return $this->belongsToMany(Product::class, 'activity_articles', 'activity_id', 'article_id');
    }
    
 

    public function productGroup()
    {
        return $this->belongsTo(ArticleGroup::class, 'product_id');
    }

    public function taskPhase()
    {
        return $this->belongsTo(TaskPhase::class, 'phase_id');
    }


   public function appointments()
    {
        return $this->belongsToMany(Appointment::class, 'activity_employees', 'activity_id', 'employee_id')
                    ->withPivot('phase_id')
                    ->withTimestamps();
    }

    public function stage()
    {
        return $this->belongsTo(\App\Models\Stage::class, 'stage_id');
    }


     public function masterSets()
    {
        return $this->hasMany(MasterSet::class, 'phase_activity_id');
    }

    public function leadStage()
    {
        return $this->belongsTo(\App\Models\LeadStage::class, 'lead_stage_id');
    }

    public function leadSubStage()
    {
        return $this->belongsTo(\App\Models\LeadStageSubStage::class, 'lead_sub_stage_id');
    }



}
