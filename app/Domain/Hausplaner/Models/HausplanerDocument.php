<?php

namespace App\Domain\Hausplaner\Models;

use App\Models\LeadAlternativeAdd;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Hausplaner (ticket) — aktueller Plan je OBJEKT (genau einer; alternative_id unique).
 * Transplantiert aus playground; EINZIGE Anker-Änderung ▲T1: project_id → alternative_id
 * (Objekt = lead_alternative_adds, kanonische Gebäudeakte).
 * scene_json = SceneDocument (mm-Integer). revision = Konfliktschutz (base_revision → 409).
 */
class HausplanerDocument extends Model
{
    protected $table = 'hausplaner_documents';

    protected $fillable = [
        'alternative_id', 'schema_version', 'revision', 'scene_json', 'checksum',
        'created_by', 'updated_by',
    ];

    protected $casts = [
        'schema_version' => 'integer',
        'revision' => 'integer',
        'scene_json' => 'array',
    ];

    /** Anker: das Objekt (Gebäudeakte). */
    public function objekt(): BelongsTo
    {
        return $this->belongsTo(LeadAlternativeAdd::class, 'alternative_id');
    }

    public function snapshots(): HasMany
    {
        return $this->hasMany(HausplanerSnapshot::class, 'hausplaner_document_id')->orderByDesc('revision');
    }
}
