<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;

    protected $fillable = [
        'position',
        'description',
        'status',
        'qualification_id',
        'qualification',
        'price',
    ];

    public function employees()
    {
        return $this->belongsToMany(
            Employee::class,
            'department_positions',
            'position_id',
            'employee_id'
        )->withPivot([
            'department_id',
            'percent',
            'montage_percent',
            'office_percent',
            'working_hours',
            'main',
        ])->withTimestamps();
    }

    public function products()
    {
        return $this->hasMany(ProductPosition::class);
    }

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'department_positions_junctions')
            ->withTimestamps();
    }

    public function departmentPositions()
    {
        return $this->hasMany(DepartmentPosition::class, 'position_id');
    }

    public function qualificationRef()
    {
        return $this->belongsTo(PositionQualification::class, 'qualification_id');
    }
}