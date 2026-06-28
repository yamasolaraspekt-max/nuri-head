<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PositionQualificationHierarchy extends Model
{
    protected $fillable = [
        'performer_qualification_id',
        'required_qualification_id',
        'allowed',
        'efficiency_factor',
        'cost_factor',
        'notes',
    ];

    protected $casts = [
        'performer_qualification_id' => 'integer',
        'required_qualification_id' => 'integer',
        'allowed' => 'boolean',
        'efficiency_factor' => 'decimal:4',
        'cost_factor' => 'decimal:4',
    ];

    public function performerQualification()
    {
        return $this->belongsTo(PositionQualification::class, 'performer_qualification_id');
    }

    public function requiredQualification()
    {
        return $this->belongsTo(PositionQualification::class, 'required_qualification_id');
    }

    public static function isAllowed(int $performerQualificationId, int $requiredQualificationId): bool
    {
        if ($performerQualificationId === $requiredQualificationId) {
            return true;
        }

        return static::query()
            ->where('performer_qualification_id', $performerQualificationId)
            ->where('required_qualification_id', $requiredQualificationId)
            ->where('allowed', true)
            ->exists();
    }

    public static function ruleFor(int $performerQualificationId, int $requiredQualificationId): ?self
    {
        if ($performerQualificationId === $requiredQualificationId) {
            return new self([
                'performer_qualification_id' => $performerQualificationId,
                'required_qualification_id' => $requiredQualificationId,
                'allowed' => true,
                'efficiency_factor' => 1,
                'cost_factor' => 1,
            ]);
        }

        return static::query()
            ->where('performer_qualification_id', $performerQualificationId)
            ->where('required_qualification_id', $requiredQualificationId)
            ->first();
    }
}