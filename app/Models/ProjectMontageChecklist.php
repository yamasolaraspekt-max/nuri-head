<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;



class ProjectMontageChecklist extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_id', 'employee_id', 'plan_montage', 'supplier_section', 'list_name',
        'cran_section', 'old_facility', 'photo_section', 'commission', 'status', 'default_stage'
    ];

    public function phaseLists()
    {
        return $this->hasMany(ProjectMontagePhaseList::class, 'project_montage_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

       public function articleGroup()
    {
        return $this->belongsTo(ArticleGroup::class, 'product_id');
    }
}
