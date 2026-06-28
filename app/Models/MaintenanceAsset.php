<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\AuditableLead;
class MaintenanceAsset extends Model
{
    use SoftDeletes;
    use AuditableLead;

    protected $fillable = [
        'lead_id',
        'alternative_id',
        'lead_product_list_id',
        'product_id',
        'maintenance_contract_id',
        'maintenance_checklist_id',
        'asset_no',
        'title',
        'serial_no',
        'manufacturer',
        'model',
        'status',
        'installation_date',
        'warranty_until',
        'location_name',
        'location_note',
        'power_kw',
        'volume_liters',
        'technical_data',
        'meta',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'installation_date' => 'date',
        'warranty_until'    => 'date',
        'technical_data'    => 'array',
        'meta'              => 'array',
    ];

    public function lead()
    {
        return $this->belongsTo(NewLeads::class, 'lead_id');
    }

    public function alternative()
    {
        return $this->belongsTo(LeadAlternativeAdd::class, 'alternative_id');
    }

    public function leadProductList()
    {
        return $this->belongsTo(LeadProductList::class, 'lead_product_list_id');
    }

    public function product()
    {
        return $this->belongsTo(ArticleGroup::class, 'product_id');
    }

    public function contract()
    {
        return $this->belongsTo(MaintenanceContract::class, 'maintenance_contract_id');
    }

    public function defaultChecklist()
    {
        return $this->belongsTo(MaintenanceChecklist::class, 'maintenance_checklist_id');
    }

    public function protocols()
    {
        return $this->hasMany(MaintenanceProtocol::class, 'maintenance_asset_id');
    }

    public function documents()
    {
        return $this->morphMany(MaintenanceDocument::class, 'documentable');
    }

  
    public function responsibleEmployee()
    {
    return $this->belongsTo(\App\Models\Employee::class, 'responsible_employee_id');
    }

    public function checklist()
    {
    return $this->belongsTo(\App\Models\MaintenanceChecklist::class, 'maintenance_checklist_id');
    }

}
