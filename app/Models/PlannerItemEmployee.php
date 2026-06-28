<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlannerItemEmployee extends Model
{
    protected $table = 'planner_item_employees';

    protected $fillable = ['planner_item_id','employee_id','role'];

    public function item()
    {
        return $this->belongsTo(PlannerItem::class, 'planner_item_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

}
