<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepartmentPosition extends Model
{
    use HasFactory;

     protected $fillable = [
        'employee_id',
        'department_id',
        'position_id',
        'percent', 
        'main', 
        'montage_percent',
        'office_percent',
    ];


    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    public function departmentPositions()
    {
        return $this->hasMany(DepartmentPosition::class);
    }

     public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
