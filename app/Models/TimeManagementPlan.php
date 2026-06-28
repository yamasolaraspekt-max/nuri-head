<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeManagementPlan extends Model
{
    protected $fillable = [
        'employee_id',
        'year',
        'month',
        'working_type',
        'hourly_rate',
        'target_hours',
        'scheduled_hours',
        'status',
        'approved_by',
        'approved_at',
        'comment',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function entries()
    {
        return $this->hasMany(TimeManagementEntry::class, 'plan_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
