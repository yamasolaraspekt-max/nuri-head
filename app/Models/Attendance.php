<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'employee_id',
        'planner_plan_id',
        'customer_id',
        'lead_product_list_id',
        'alternative_id',
        'product_id',
        'article_group',
        'date',
        'status',
        'check_in',
        'check_out',
        'travel_started_at',
        'arrived_at',
        'work_started_at',
        'work_ended_at',
        'pause_started_at',
        'pause_type',
        'travel_total_seconds',
        'pause_total_seconds',
        'work_total_seconds',
        'destination',
        'destination_lat',
        'destination_lng',
        'current_lat',
        'current_lng',
        'last_location_at',
        'created_by',
        'updated_by',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'date' => 'date',
        'travel_started_at' => 'datetime',
        'arrived_at' => 'datetime',
        'work_started_at' => 'datetime',
        'work_ended_at' => 'datetime',
        'pause_started_at' => 'datetime',
        'last_location_at' => 'datetime',
        'travel_total_seconds' => 'integer',
        'pause_total_seconds' => 'integer',
        'work_total_seconds' => 'integer',
        'destination_lat' => 'decimal:8',
        'destination_lng' => 'decimal:8',
        'current_lat' => 'decimal:8',
        'current_lng' => 'decimal:8',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function plan()
    {
        return $this->belongsTo(PlannerPlan::class, 'planner_plan_id');
    }

    public function events()
    {
        return $this->hasMany(AttendanceEvent::class);
    }

    public function locations()
    {
        return $this->hasMany(AttendanceLocation::class);
    }
}
