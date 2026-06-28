<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceChecklistItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'maintenance_checklist_id',
        'label',
        'field_name',
        'field_type',
        'options',
        'is_required',
        'help_text',
        'placeholder',
        'file_accept',
        'sort_order',
    ];

    protected $casts = [
        'options'     => 'array',
        'is_required' => 'boolean',
    ];

    public function checklist()
    {
        return $this->belongsTo(MaintenanceChecklist::class, 'maintenance_checklist_id');
    }
}
