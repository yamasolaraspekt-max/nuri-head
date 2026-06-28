<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobRepresentative extends Model
{
    use HasFactory;

   protected $fillable = [
        'employee_id', 'department_id', 'position_id', 'representer_id', 'current_representer', 'start_date', 'end_date', 'description', 'status'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function representer()
    {
        return $this->belongsTo(Employee::class, 'representer_id');
    }

    public function currentRepresenter()
    {
        return $this->belongsTo(Employee::class, 'current_representer');
    }
}
