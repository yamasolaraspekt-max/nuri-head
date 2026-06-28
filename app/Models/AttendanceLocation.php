<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceLocation extends Model
{
    protected $fillable = [
        'attendance_id',
        'employee_id',
        'planner_plan_id',
        'lat',
        'lng',
        'accuracy',
        'speed',
        'heading',
        'destination',
        'recorded_at',
        'meta',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
        'meta' => 'array',
        'lat' => 'decimal:8',
        'lng' => 'decimal:8',
        'accuracy' => 'decimal:2',
        'speed' => 'decimal:2',
        'heading' => 'decimal:2',
    ];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }
}
