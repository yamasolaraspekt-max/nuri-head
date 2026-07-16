<?php

namespace App\Models;

use App\Services\BuildingModel\BuildingModelVersionImmutableException;
use Illuminate\Database\Eloquent\Model;

/**
 * G1b-1 — unveränderliche, abgeleitete Version eines kanonischen Gebäudemodells (derived_only Projektion).
 *
 * Immutability (Applikationsebene): jedes Update oder Delete über Eloquent wird abgewehrt; eine neue Fassung
 * erzeugt eine neue Version. Kein Soft-Delete, kein `updated_at` (append-only). Die Zeile ist KEIN aktiver
 * fachlicher Writer — `projection_role` ist auf `derived_only` festgeschrieben. Die kanonische Schreibwahrheit
 * bleibt `anforderungsprofile.gebaeude_geometrie`.
 *
 * Hinweis (im Manifest offengelegt): Die Unveränderlichkeit wird auf Applikationsebene erzwungen; die
 * Migration bleibt portabel und führt KEINE DB-Trigger ein.
 */
class BuildingModelVersion extends Model
{
    public const ROLE_DERIVED_ONLY = 'derived_only';

    /** Append-only: nur created_at, kein updated_at. */
    public const UPDATED_AT = null;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'revision_id',
        'model_id',
        'parent_revision_id',
        'object_id',
        'source_type',
        'source_profile_id',
        'source_version',
        'source_hash',
        'source_captured_at',
        'schema_version',
        'projection_version',
        'payload',
        'payload_hash',
        'validation_status',
        'warnings',
        'projected_at',
        'projection_role',
    ];

    protected $casts = [
        'object_id' => 'integer',
        'source_profile_id' => 'integer',
        'source_version' => 'integer',
        'payload' => 'array',
        'warnings' => 'array',
        'source_captured_at' => 'datetime',
        'projected_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::updating(function (): void {
            throw new BuildingModelVersionImmutableException(
                'building_model_versions ist unveränderlich — In-place-Update ist nicht zulässig; eine neue Fassung erzeugt eine neue Version.'
            );
        });

        static::deleting(function (): void {
            throw new BuildingModelVersionImmutableException(
                'building_model_versions ist unveränderlich — Löschen ist nicht zulässig (append-only, kein Soft-Delete).'
            );
        });
    }
}
