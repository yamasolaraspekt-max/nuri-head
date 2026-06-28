<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int|null         $id
 * @property string|null      $stage              // 'inquiry','lead','offer','deals','project','ticket' (EN, stored)
 * @property int|null         $article_group_id
 * @property int|null         $service_id
 * @property int|null         $department_id
 * @property array|null       $position_ids       // ['internal'=>[...], 'external'=>[...]]
 * @property-read string|null $stage_label_de     // e.g. 'Anfrage', 'Angebot'
 * @property-read array       $position_names     // resolved names (['internal'=>[...], 'external'=>[...]])
 */
class ProductPosition extends Model
{
    use HasFactory;

    protected $fillable = [
        'stage',
        'article_group_id',
        'department_id',
        'position_ids',
        'service_id',
    ];

    protected $casts = [
        'position_ids' => 'array', // JSON ⇄ array
    ];

    // Provide a sane default JSON shape
    protected $attributes = [
        'position_ids' => '{"internal":[],"external":[]}',
    ];

    // Expose computed German label on the model JSON
    protected $appends = [
        'stage_label_de',
    ];

    /* -------------------------------------------------
     | Relationships
     |--------------------------------------------------*/
    public function articleGroup()
    {
        return $this->belongsTo(ArticleGroup::class, 'article_group_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function service()
    {
        return $this->belongsTo(PhaseSection::class, 'service_id');
    }

    // Removed: product(), position()
    // Reason: this record does not belong to a single Product or Position.
    // - Product is represented via ArticleGroup + PhaseSection in your flow.
    // - Positions live inside JSON (multiple), so no single belongsTo Position.

    /* -------------------------------------------------
     | Stage: EN (DB) → DE (UI)
     |--------------------------------------------------*/
    public const STAGE_LABELS_DE = [
        'inquiry' => 'Anfrage',
        'lead'    => 'Lead',
        'offer'   => 'Angebot',
        'deals'   => 'Abschluss',
        'project' => 'Projekt',
        'ticket'  => 'Ticket',
    ];

    public function getStageLabelDeAttribute(): ?string
    {
        if (!$this->stage) {
            return null;
        }
        $key = strtolower($this->stage);
        return self::STAGE_LABELS_DE[$key] ?? $this->stage;
    }

    /* -------------------------------------------------
     | Position helpers (JSON)
     |--------------------------------------------------*/
    public function internalPositionIds(): array
    {
        return array_values(array_filter($this->position_ids['internal'] ?? []));
    }

    public function externalPositionIds(): array
    {
        return array_values(array_filter($this->position_ids['external'] ?? []));
    }

    /**
     * Resolve position names for both buckets in one call.
     * Returns: ['internal' => ['…','…'], 'external' => ['…','…']]
     */
    public function getPositionNamesAttribute(): array
    {
        $internalIds = $this->internalPositionIds();
        $externalIds = $this->externalPositionIds();

        $namesInternal = $internalIds
            ? Position::whereIn('id', $internalIds)->pluck('position')->toArray()
            : [];

        $namesExternal = $externalIds
            ? Position::whereIn('id', $externalIds)->pluck('position')->toArray()
            : [];

        return [
            'internal' => $namesInternal,
            'external' => $namesExternal,
        ];
    }

    /* -------------------------------------------------
     | Scopes (optional sugar)
     |--------------------------------------------------*/
    public function scopeStage($query, string $stage)
    {
        return $query->where('stage', $stage); // stage stays EN
    }

    public function scopeForDepartment($query, int $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }
}
