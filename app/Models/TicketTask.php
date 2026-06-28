<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id','employee_id','error_id','appointment_id',
        'title','priority','status','start_date','due_date','difference',
        'solution','is_done','teams',
    ];


    // Relationships

    // Belongs to a Problem (ticket)
    public function ticket()
    {
        return $this->belongsTo(Problem::class, 'ticket_id');
    }

    // Belongs to an Employee
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // Belongs to an Error
    public function error()
    {
        return $this->belongsTo(Error::class);
    }

    protected $casts = [
        'is_done' => 'boolean',
        'start_date' => 'date',
        'due_date' => 'date',
    ];

    public function appointment(){ return $this->belongsTo(MainAppointment::class, 'appointment_id'); }

    public function appointments()
    {
        return $this->hasMany(MainAppointment::class, 'problem_task_id');
    }

}

