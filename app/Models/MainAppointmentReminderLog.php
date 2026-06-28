<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MainAppointmentReminderLog extends Model
{
    protected $fillable = [
        'appointment_id',
        'employee_id',
        'reminder_at',
        'reminder_count',
        'last_reminded_at',
        'seen_at',
    ];

    protected $casts = [
        'reminder_at' => 'datetime',
        'last_reminded_at' => 'datetime',
        'seen_at' => 'datetime',
        'reminder_count' => 'integer',
    ];

    public function appointment()
    {
        return $this->belongsTo(MainAppointment::class, 'appointment_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}