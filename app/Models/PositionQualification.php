<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PositionQualification extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'default_price',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'default_price' => 'decimal:2',
        'sort_order' => 'integer',
    ];

    public function positions()
    {
        return $this->hasMany(Position::class, 'qualification_id');
    }

    /**
     * What this qualification can perform.
     * Example: Meister can perform Geselle, Helfer, Azubi.
     */
    public function canPerformRules()
    {
        return $this->hasMany(PositionQualificationHierarchy::class, 'performer_qualification_id');
    }

    /**
     * Who can perform this qualification's work.
     * Example: Geselle work can be done by Geselle or Meister.
     */
    public function performedByRules()
    {
        return $this->hasMany(PositionQualificationHierarchy::class, 'required_qualification_id');
    }

    public function canPerformQualifications()
    {
        return $this->belongsToMany(
            PositionQualification::class,
            'position_qualification_hierarchies',
            'performer_qualification_id',
            'required_qualification_id'
        )->withPivot([
            'efficiency_factor',
            'cost_factor',
            'allowed',
            'notes',
        ])->withTimestamps();
    }

    public function canBePerformedByQualifications()
    {
        return $this->belongsToMany(
            PositionQualification::class,
            'position_qualification_hierarchies',
            'required_qualification_id',
            'performer_qualification_id'
        )->withPivot([
            'efficiency_factor',
            'cost_factor',
            'allowed',
            'notes',
        ])->withTimestamps();
    }
}