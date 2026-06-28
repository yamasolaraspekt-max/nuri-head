<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceProtocol extends Model
{
    use SoftDeletes;

 protected $fillable = [
        'lead_id',
        'alternative_id',
        'maintenance_asset_id',
        'customer_maintenance_contract_id',
        'maintenance_checklist_id',
        'protocol_no',
        'title',
        'scheduled_at',
        'started_at',
        'finished_at',
        'status',
        'checklist_snapshot',
        'answers',
        'result_summary',
        'internal_note',
        'meta',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'scheduled_at'       => 'datetime',
        'started_at'         => 'datetime',
        'finished_at'        => 'datetime',
        'checklist_snapshot' => 'array',
        'answers'            => 'array',
        'meta'               => 'array',
    ];

    public function lead()
    {
        return $this->belongsTo(NewLeads::class, 'lead_id');
    }

    public function alternative()
    {
        return $this->belongsTo(LeadAlternativeAdd::class, 'alternative_id');
    }

    public function asset()
    {
        return $this->belongsTo(MaintenanceAsset::class, 'maintenance_asset_id');
    }

    public function contract()
    {
        return $this->belongsTo(MaintenanceContract::class, 'maintenance_contract_id');
    }

    public function checklist()
    {
        return $this->belongsTo(MaintenanceChecklist::class, 'maintenance_checklist_id');
    }

    public function technicians()
    {
        return $this->belongsToMany(Employee::class, 'maintenance_protocol_technicians')
            ->using(MaintenanceProtocolTechnician::class)
            ->withPivot([
                'role',
                'status',
                'planned_hours',
                'actual_hours',
                'started_at',
                'finished_at',
                'meta',
            ])
            ->withTimestamps();
    }

    public function documents()
    {
        return $this->morphMany(MaintenanceDocument::class, 'documentable');
    }

    public function customerContract()
    {
        return $this->belongsTo(
            CustomerMaintenanceContract::class,
            'maintenance_protocol_id'
        );
    }
 
    public function leadTechnician()
    {
    return $this->technicians()->wherePivot('role', 'lead');
    }

    public function appointment()
    {
    return $this->belongsTo(\App\Models\MainAppointment::class, 'appointment_id');
    }

}
