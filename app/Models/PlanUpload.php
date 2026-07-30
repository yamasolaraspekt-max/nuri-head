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

    /**
     * AUF-88-P1 — die Insel-Sicht auf diesen Upload, EINE Wahrheit für zwei Aufrufstellen
     * (`HausplanerController::hausplanerUnterlage()` beim Seitenaufruf,
     * `PlanUploadController::status()` beim Polling nach dem Hochladen). Dieselbe Form, egal
     * woher sie kommt — die Insel liest sie einmal, in `state/unterlage.ts`.
     *
     * **Kein Bildinhalt hier** — nur die URL zum bestehenden `bild()`-Endpunkt, der sein eigenes
     * Ownership-Gate trägt.
     *
     * @return array<string, mixed>
     */
    public function alsUnterlage(): array
    {
        // Ein Bild ist nur erreichbar, wenn es wirklich eines gibt (typ=bild) oder eine PDF-
        // Rasterung gelungen ist (meta.bild_pfad) — sonst zeigt die Insel den Grund, nicht ein
        // kaputtes Bild.
        $bildDa = $this->typ === 'bild' || (bool) data_get($this->meta, 'bild_pfad');

        return [
            'id' => $this->id,
            'status' => $this->status,
            'typ' => $this->typ,
            'originalName' => $this->original_name,
            'hochgeladenAm' => optional($this->created_at)->toIso8601String(),
            'massstabMmProEinheit' => $this->massstab_mm_pro_einheit,
            'bildUrl' => $bildDa ? route('energie.plan-upload.bild', $this) : null,
            'massstabUrl' => route('energie.plan-upload.massstab', $this),
            'statusUrl' => route('energie.plan-upload.status', $this),
            'fehler' => data_get($this->meta, 'fehler') ?? data_get($this->meta, 'rasterung_fehler'),
            'importDienstNoetig' => ! $bildDa && $this->status === 'klassifiziert' && $this->typ === 'pdf',
        ];
    }
}
