<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterSetLabor extends Model
{
    protected $table = 'master_set_labor';

    protected $fillable = [
        'master_set_id',
        'department_id',
        'qualification_id',
        'position_id',
        'employee_id',
        'hourly_rate',
        'hours',
        'sort_order',
    ];

    protected $casts = [
        'hourly_rate' => 'decimal:2',
        'hours' => 'decimal:2',
    ];

    public function qualification()
    {
        return $this->belongsTo(\App\Models\PositionQualification::class, 'qualification_id');
    }
    public function masterSet()
    {
        return $this->belongsTo(MasterSet::class, 'master_set_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
