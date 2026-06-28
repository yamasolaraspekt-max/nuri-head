<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\DepartmentPosition;
use App\Models\Employee;
use App\Models\Position;


class Department extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable=[
        'department_name','parent_id', 'branch_id', 'description', 'order', 'department_head','head_representative'
    ];


    public function employee()
    {
        return $this->belongsTo(Employee::class, 'department_head');
    }

    public function representative()
    {
        return $this->belongsTo(Employee::class, 'head_representative');
    }


    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
     public function children()
    {
        return $this->hasMany(Department::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Department::class, 'parent_id');
    }

     public function positions()
    {
        return $this->belongsToMany(Position::class, 'department_positions_junctions')
                    ->withTimestamps(); // Uses the pivot table
    }

    public function suggestedEmployees()
    {
        return $this->hasMany(\App\Models\CustomerSuggestEmployee::class, 'department_id');
    }

  public function employees()
{
    return $this->belongsToMany(
        Employee::class,
        'department_positions',
        'department_id',
        'employee_id'
    )->withPivot([
        'position_id',
        'percent',
        'montage_percent',
        'office_percent',
        'working_hours',
        'main',
    ])->withTimestamps();
}

    public function departmentPositions()
    {
        return $this->hasMany(DepartmentPosition::class, 'department_id');
    }


        public function staff()
    {
        return $this->employees();
    }
 
 
    public function goodsReceipts()
    {
        return $this->hasMany(\App\Models\GoodsReceipt::class, 'department_id');
    }

    
 public function offerTemplates()
{
    return $this->hasMany(\App\Models\OfferTemplate::class, 'department_id');
}

    public function machines()
    {
        return $this->hasMany(\App\Models\Machine::class, 'department_id');
    }



}
