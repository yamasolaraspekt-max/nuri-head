<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceProtocolTechnician extends Model
{
    protected $table = 'maintenance_protocol_technicians';

    protected $fillable = [
        'maintenance_protocol_id',
        'employee_id',
        'role',
        'status',
        'planned_hours',
        'actual_hours',
        'started_at',
        'finished_at',
        'meta',
    ];

    protected $casts = [
        'planned_hours' => 'float',
        'actual_hours'  => 'float',
        'started_at'    => 'datetime',
        'finished_at'   => 'datetime',
        'meta'          => 'array',
    ];

    public function protocol()
    {
        // Make sure this model exists: App\Models\MaintenanceProtocol
        return $this->belongsTo(MaintenanceProtocol::class, 'maintenance_protocol_id');
    }

    public function employee()
    {
        // Make sure App\Models\Employee exists
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
