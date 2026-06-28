<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\AuditableLead;
class MainAppointmentEmployee extends Model {
    use HasFactory;use AuditableLead;

    protected $fillable = ['employee_id', 'appointment_id','status','reason' ];

    public function appointments() {
        return $this->belongsToMany(MainAppointment::class, 'main_appointment_employees')
                    ->withPivot('status', 'reason')
                    ->withTimestamps();
    }

    public function appointment()
    {
        return $this->belongsTo(MainAppointment::class, 'appointment_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
