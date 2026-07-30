<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Hochgeladener Plan (DWG/DXF/PDF/Bild) als Import-Quelle für den Grundriss-Editor.
 * Transplantat aus wberechnung (Plan-Import-Track).
 */
class PlanUpload extends Model
{
    /** @var list<string> */
    protected $fillable = [
        'user_id', 'heizlast_projekt_id', 'lead_alternative_add_id', 'original_name', 'pfad',
        'mime', 'groesse_bytes', 'typ', 'status', 'massstab_mm_pro_einheit', 'kandidat_geometrie',
        'konfidenz', 'meta',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'groesse_bytes' => 'integer',
            'massstab_mm_pro_einheit' => 'float',
            'kandidat_geometrie' => 'array',
            'meta' => 'array',
        ];
    }

    /** @return BelongsTo<HeizlastProjekt, $this> */
    public function heizlastProjekt(): BelongsTo
    {
        return $this->belongsTo(HeizlastProjekt::class);
    }

    /**
     * AUF-88-P1 / K-02 — das Hausplaner-Objekt, dem diese Referenzunterlage zugeordnet ist.
     *
     * @return BelongsTo<LeadAlternativeAdd, $this>
     */
    public function hausplanerObjekt(): BelongsTo
    {
        return $this->belongsTo(LeadAlternativeAdd::class, 'lead_alternative_add_id');
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
