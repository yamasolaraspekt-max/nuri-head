<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterSetChecklist extends Model
{
    protected $table = 'master_set_checklists';

    protected $fillable = [
        'master_set_id',
        'maintenance_checklist_id',
        'trigger',
        'is_required',
        'sort_order',
        'checklist_title_snapshot',
        'checklist_type_snapshot',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'sort_order' => 'integer',
        'master_set_id' => 'integer',
        'maintenance_checklist_id' => 'integer',
    ];

    public function checklist()
    {
        return $this->belongsTo(MaintenanceChecklist::class, 'maintenance_checklist_id');
    }

    public function masterSet()
    {
        return $this->belongsTo(MasterSet::class, 'master_set_id');
    }
}
