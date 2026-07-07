<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Sanierungs-Szenario zu einem Heizlast-Projekt. Die Maßnahmen werden beim Vergleich
 * auf eine In-Memory-Kopie des Projekts angewandt (keine Mutation echter Bauteile).
 * Transplantat aus wberechnung.
 */
class SanierungsVariante extends Model
{
    protected $table = 'sanierungs_varianten';

    /** @var list<string> */
    protected $fillable = [
        'heizlast_projekt_id', 'label', 'ist_basis', 'basis_variante_id',
        'massnahmen', 'ergebnis_snapshot', 'reihenfolge',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'massnahmen' => 'array',
            'ergebnis_snapshot' => 'array',
            'ist_basis' => 'boolean',
        ];
    }

    /** @return BelongsTo<HeizlastProjekt, $this> */
    public function heizlastProjekt(): BelongsTo
    {
        return $this->belongsTo(HeizlastProjekt::class);
    }

    /** @return BelongsTo<SanierungsVariante, $this> */
    public function basis(): BelongsTo
    {
        return $this->belongsTo(SanierungsVariante::class, 'basis_variante_id');
    }
}
