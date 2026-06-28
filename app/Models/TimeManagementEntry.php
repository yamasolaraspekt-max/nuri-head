<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeManagementEntry extends Model
{
    protected $fillable = [
        'plan_id',
        'work_date',
        'start_time',
        'end_time',
        'break_minutes',
        'hours',
    ];

    protected $casts = [
        'work_date' => 'date',
    ];

    public function plan()
    {
        return $this->belongsTo(TimeManagementPlan::class, 'plan_id');
    }
}
