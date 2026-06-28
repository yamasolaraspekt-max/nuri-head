<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterSetTask extends Model
{
    protected $table = 'master_set_tasks';

    protected $fillable = [
        'master_set_id',

        // Legacy column. Keep it for old rows, but new saves should use lead_stage_id.
        'stage_id',

        // New workflow columns.
        'lead_stage_id',
        'lead_sub_stage_id',

        'task_phase_id',
        'phase_activity_id',
        'stage_name',
        'phase_name',
        'title',
        'description',
        'duration',
        'duration_type',
        'notes',
        'priority',
        'percent',
        'hours',
        'sort_order',
    ];

    protected $casts = [
        'master_set_id' => 'integer',
        'stage_id' => 'integer',
        'lead_stage_id' => 'integer',
        'lead_sub_stage_id' => 'integer',
        'task_phase_id' => 'integer',
        'phase_activity_id' => 'integer',
        'percent' => 'float',
        'hours' => 'float',
        'sort_order' => 'integer',
    ];

    public function masterSet(): BelongsTo
    {
        return $this->belongsTo(MasterSet::class, 'master_set_id');
    }

    /**
     * Legacy relation. Do not use for new workflow screens.
     */
    public function legacyStage(): BelongsTo
    {
        return $this->belongsTo(Stage::class, 'stage_id');
    }

    public function leadStage(): BelongsTo
    {
        return $this->belongsTo(LeadStage::class, 'lead_stage_id');
    }

    public function leadSubStage(): BelongsTo
    {
        return $this->belongsTo(LeadStageSubStage::class, 'lead_sub_stage_id');
    }

    public function taskPhase(): BelongsTo
    {
        return $this->belongsTo(TaskPhase::class, 'task_phase_id');
    }

    public function phaseActivity(): BelongsTo
    {
        return $this->belongsTo(PhaseActivities::class, 'phase_activity_id');
    }

    public function labor(): HasMany
    {
        return $this->hasMany(MasterSetTaskLabor::class, 'master_set_task_id');
    }

    public function taskLabors(): HasMany
    {
        return $this->hasMany(MasterSetTaskLabor::class, 'master_set_task_id');
    }

    public function getEffectiveLeadStageIdAttribute(): ?int
    {
        return $this->lead_stage_id ?: $this->stage_id;
    }

    public function getEffectiveStageNameAttribute(): ?string
    {
        return $this->stage_name ?: $this->leadStage?->name ?: $this->legacyStage?->stage;
    }
}
