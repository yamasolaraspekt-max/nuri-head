<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Hüllbauteil eines Raums mit U-Wert-Strategie (A/B/C) und Quelle/Konfidenz.
 * Transplantat aus wberechnung. Die optionale konstruktion()-Relation zeigt auf das
 * ticket-eigene Konstruktion-Model (Tabelle konstruktionen).
 */
class HeizlastBauteil extends Model
{
    protected $table = 'heizlast_bauteile';

    /** @var list<string> */
    protected $fillable = [
        'heizlast_raum_id', 'typ', 'grenzflaeche', 'azimut_grad', 'flaeche_m2',
        'u_strategie', 'u_wert', 'schichten', 'fenster_typ', 'konstruktion_id', 'konfidenz', 'quelle', 'reihenfolge',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'flaeche_m2' => 'float',
            'u_wert' => 'float',
            'azimut_grad' => 'float',
            'konstruktion_id' => 'integer',
            'schichten' => 'array',
            'reihenfolge' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<HeizlastRaum, $this>
     */
    public function raum(): BelongsTo
    {
        return $this->belongsTo(HeizlastRaum::class, 'heizlast_raum_id');
    }

    /**
     * @return BelongsTo<Konstruktion, $this>
     */
    public function konstruktion(): BelongsTo
    {
        return $this->belongsTo(Konstruktion::class);
    }
}
