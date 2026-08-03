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
    /**
     * Die aktuelle Schema-Version — **eine Stelle in PHP, mehrere Leser.**
     *
     * Vor Z-06-N1/K-N5 stand die 3 als Zeichenkette in der Request-Regel (`in:3`) und wurde beim
     * Rueckweg gar nicht gekannt. *Eine Version, die an zwei Orten getippt wird, laeuft beim
     * naechsten Anheben auseinander — und zwar still, weil beide fuer sich gruen bleiben.*
     * Die TypeScript-Seite fuehrt dieselbe Zahl in `domain/scene.types.ts` (`SCHEMA_VERSION`);
     * die zwei Sprachen koennen sich keine Konstante teilen, aber je Sprache genuegt eine.
     */
    public const SCHEMA_VERSION = 3;

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
