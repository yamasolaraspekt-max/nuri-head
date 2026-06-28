<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppointmentEmployee extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'employee_id',
    ];

    // Relationships
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
