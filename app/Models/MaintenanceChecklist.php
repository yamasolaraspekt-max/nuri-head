<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceChecklist extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'logo_path',
        'type',
        'status',
        'is_global',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_global' => 'boolean',
    ];

    public function items()
    {
        return $this->hasMany(MaintenanceChecklistItem::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function brands()
    {
        return $this->belongsToMany(Brand::class, 'brand_maintenance_checklist')
            ->withTimestamps();
    }

    public function distributors()
    {
        return $this->belongsToMany(Distributor::class, 'distributor_maintenance_checklist')
            ->withTimestamps();
    }

    public function creatorEmployee()
    {
        return $this->belongsTo(\App\Models\Employee::class, 'created_by');
    }

     public function assets()
    {
        return $this->hasMany(MaintenanceAsset::class, 'maintenance_checklist_id');
    }

    public function protocols()
    {
        return $this->hasMany(MaintenanceProtocols::class, 'maintenance_checklist_id');
    }

    public function masterSets()
    {
        return $this->belongsToMany(\App\Models\MasterSet::class, 'master_set_checklists')
            ->withPivot(['id','trigger','is_required','sort_order'])
            ->withTimestamps();
    }
}
