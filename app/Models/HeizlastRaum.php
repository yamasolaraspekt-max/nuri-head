<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Raum eines Heizlast-Projekts. Transplantat aus wberechnung. Die geometrie()-Relation
 * (RaumGeometrie) aus wberechnung entfällt hier bewusst – der Grundriss ist eine spätere
 * Welle und die Tabelle raum_geometrien wird in diesem Schritt nicht gebaut.
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
}
