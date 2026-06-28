<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskPhase extends Model
{
    use HasFactory;
    use SoftDeletes;
        protected $fillable = [
            'product_id',
            'section_id',
            'section_name',
            'phase_name',
            'stage',
            'stage_id',
            'version',
            'status',
            'count',
            'order',
            'lead_stage_id',
            'lead_sub_stage_id',
            'description',
        ];

    public function product()
    {
        return $this->belongsTo(ArticleGroup::class, 'product_id');
    }

   // 🔗 Relation: belongs to section
    public function section()
    {
        return $this->belongsTo(PhaseSection::class, 'section_id');
    }

    // 🔗 Relation: has many phase activities
    public function activities()
    {
        return $this->hasMany(PhaseActivities::class, 'phase_id');
    }

       // Define relationship with ArticleGroup
    public function articleGroup()
    {
        return $this->belongsTo(ArticleGroup::class, 'product_id');
    }
     public function customerPhaseList()
    {
        return $this->hasOne(CustomerPhaseList::class, 'phase_id');
    }

    

    public function stage()
    {
        return $this->belongsTo(Stage::class, 'stage_id');
    } 
     public function taskDocuments()
    {
        return $this->hasMany(TaskDocument::class, 'phase_id');
    }

     public function customerPhaseStages()
    {
        return $this->hasMany(CustomerPhaseStage::class);
    }
   public function appointments()
    {
        return $this->belongsToMany(Appointment::class, 'activity_employees', 'phase_id', 'employee_id')
                    ->withPivot('activity_id')
                    ->withTimestamps();
    }

    public function suggestedEmployees()
    {
        return $this->hasMany(\App\Models\CustomerSuggestEmployee::class, 'phase_id');
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
