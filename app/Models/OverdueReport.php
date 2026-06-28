<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OverdueReport extends Model
{
    use SoftDeletes;

    protected $table = 'overdue_reports';

    protected $fillable = [
        'type',
        'target_id',
        'report',
        'employee_id',
    ];

    protected $casts = [
        'employee_id' => 'integer',
        'target_id'   => 'integer',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
        'deleted_at'  => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    public function reads()
    {
        return $this->hasMany(OverdueReportRead::class, 'report_id', 'id');
    }
}