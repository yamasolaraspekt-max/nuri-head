<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterSetTaskLabor extends Model
{
    protected $table = 'master_set_task_labors';
    protected $guarded = [];

    protected $fillable =[
        'id', 'master_set_task_id', 'qualification_id', 'hours','rate', 'auto_sum_id', 
    ];

    public function qualification()
    {
        return $this->belongsTo(PositionQualification::class, 'qualification_id');
    }
    public function task()
    {
        return $this->belongsTo(MasterSetTask::class, 'master_set_task_id');
    }
}