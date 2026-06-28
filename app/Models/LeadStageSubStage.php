<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadStageSubStage extends Model
{
    use SoftDeletes;

    protected $table = 'lead_stage_sub_stages';

    protected $fillable = [
        'lead_stage_id',
        'key',
        'name',
        'color',
        'icon',
        'sort_order',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'lead_stage_id' => 'integer',
        'sort_order' => 'integer',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (LeadStageSubStage $subStage) {
            $subStage->name = trim((string) $subStage->name);

            if (blank($subStage->key)) {
                $subStage->key = LeadStage::makeKey($subStage->name);
            }

            if (blank($subStage->color)) {
                $subStage->color = '#93c21c';
            }

            if ($subStage->sort_order === null) {
                $subStage->sort_order = ((int) static::query()
                    ->where('lead_stage_id', $subStage->lead_stage_id)
                    ->max('sort_order')) + 10;
            }

            $subStage->is_active = $subStage->is_active ?? true;
            $subStage->is_default = $subStage->is_default ?? false;
        });

        static::saving(function (LeadStageSubStage $subStage) {
            $subStage->name = trim((string) $subStage->name);
            $subStage->key = LeadStage::makeKey((string) ($subStage->key ?: $subStage->name));
        });

        static::saved(function (LeadStageSubStage $subStage) {
            if ($subStage->is_default) {
                static::query()
                    ->where('lead_stage_id', $subStage->lead_stage_id)
                    ->whereKeyNot($subStage->id)
                    ->where('is_default', true)
                    ->update(['is_default' => false]);
            }
        });
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function stage()
    {
        return $this->belongsTo(LeadStage::class, 'lead_stage_id');
    }

    public function taskPhases()
    {
        return $this->hasMany(TaskPhase::class, 'lead_sub_stage_id', 'id');
    }

    public function usageCount(): int
    {
        return $this->taskPhases()
            ->whereNull('deleted_at')
            ->count();
    }
}
