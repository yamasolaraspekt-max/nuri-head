<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\AuditableLead;
class CustomerMaintenanceContract extends Model
{
    use SoftDeletes;
use AuditableLead;
    protected $table = 'customer_maintenance_contracts';

    protected $fillable = [
        'lead_id',
        'alternative_id',

        'maintenance_contract_id',
        'asset_id',

        'contract_no',
        'title',
        'contract_type',
        'billing_mode',

        'next_service_date',
        'start_date',
        'end_date',
        'cancelled_at',

        'interval_type',
        'interval_months',

        'status',
        'status_overall',

        'contract_duration_months',
        'termination_notice_days',
        'recommended_interval_months',

        'price',
        'currency',

        'description',
        'terms',
        'internal_notes',

        // IMPORTANT
        'payload',
        'meta',

        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'next_service_date'           => 'date',
        'start_date'                  => 'date',
        'end_date'                    => 'date',
        'cancelled_at'                => 'date',
        'price'                       => 'decimal:2',

        // IMPORTANT: these two must be here
        'payload'                     => 'array',
        'meta'                        => 'array',
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
        return $this->belongsTo(MaintenanceAsset::class, 'asset_id');
    }

    public function protocols()
    {
        return $this->hasMany(
            MaintenanceProtocol::class,
            'maintenance_contract_id',
            'maintenance_contract_id'
        );
    }
 

    public function latestProtocol()
    {
        return $this->hasOne(MaintenanceProtocol::class, 'maintenance_contract_id')->latestOfMany();
    }


    public function responsibleEmployee()
    {
    return $this->belongsTo(\App\Models\Employee::class, 'responsible_employee_id');
    }

    public function assets()
    {
    return $this->hasMany(\App\Models\MaintenanceAsset::class, 'customer_maintenance_contract_id');
    }

    // nächstes geplantes Protokoll
    public function nextPlannedProtocol()
    {
    return $this->hasOne(\App\Models\MaintenanceProtocol::class, 'customer_maintenance_contract_id')
        ->ofMany('scheduled_at', 'min', function ($q) {
        $q->whereIn('status', ['planned','in_progress'])->whereNotNull('scheduled_at');
        });
    }

}
