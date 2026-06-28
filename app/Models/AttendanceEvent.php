<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceEvent extends Model
{
    protected $fillable = [
        'attendance_id',
        'employee_id',
        'planner_plan_id',
        'planner_item_id',
        'event_type',
        'event_at',
        'lat',
        'lng',
        'destination',
        'note',
        'meta',
    ];

    protected $casts = [
        'event_at' => 'datetime',
        'meta' => 'array',
        'lat' => 'decimal:8',
        'lng' => 'decimal:8',
    ];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }
}
