<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Raum eines Heizlast-Projekts. Transplantat aus wberechnung. Die geometrie()-Relation
 * (RaumGeometrie / raum_geometrien) speist die Flächenableitung (GeometrieAbleitungService).
 */
class HeizlastRaum extends Model
{
    protected $table = 'heizlast_raeume';

    /** @var list<string> */
    protected $fillable = [
        'heizlast_projekt_id', 'name', 'nutzung', 'theta_int_c',
        'grundflaeche_m2', 'hoehe_m', 'luftwechsel_1h', 'heizkoerper', 'fussbodenheizung', 'reihenfolge',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'theta_int_c' => 'float',
            'grundflaeche_m2' => 'float',
            'hoehe_m' => 'float',
            'luftwechsel_1h' => 'float',
            'heizkoerper' => 'array',
            'fussbodenheizung' => 'array',
            'reihenfolge' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<HeizlastProjekt, $this>
     */
    public function projekt(): BelongsTo
    {
        return $this->belongsTo(HeizlastProjekt::class, 'heizlast_projekt_id');
    }

    /**
     * @return HasMany<HeizlastBauteil, $this>
     */
    public function bauteile(): HasMany
    {
        return $this->hasMany(HeizlastBauteil::class)->orderBy('reihenfolge')->orderBy('id');
    }

    /**
     * @return HasOne<RaumGeometrie, $this>
     */
    public function geometrie(): HasOne
    {
        return $this->hasOne(RaumGeometrie::class);
    }
}
