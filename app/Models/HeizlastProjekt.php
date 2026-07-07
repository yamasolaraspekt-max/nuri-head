<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Heizlast-Projekt (Gebäude) – Container der raumweisen DIN-EN-12831-Berechnung.
 * Transplantat aus wberechnung. Die energiekonzept()-Relation aus wberechnung entfällt,
 * da ticket kein Energiekonzept-Model/-Tabelle besitzt (energiekonzept_id bleibt als
 * loses, additives Feld erhalten).
 */
class HeizlastProjekt extends Model
{
    protected $table = 'heizlast_projekte';

    /** @var list<string> */
    protected $fillable = [
        'name', 'standort_plz', 'norm_aussentemp_c', 'gelaendehoehe_m', 'baujahr', 'sanierungsstufe',
        'waermebruecken', 'komfortzuschlag_k', 'intermittierend', 'ziel_vorlauf_c', 'spreizung_k',
        'energiekonzept_id', 'ergebnis',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'norm_aussentemp_c' => 'float',
            'gelaendehoehe_m' => 'float',
            'baujahr' => 'integer',
            'komfortzuschlag_k' => 'float',
            'intermittierend' => 'boolean',
            'ziel_vorlauf_c' => 'float',
            'spreizung_k' => 'float',
            'ergebnis' => 'array',
        ];
    }

    /**
     * @return HasMany<HeizlastRaum, $this>
     */
    public function raeume(): HasMany
    {
        return $this->hasMany(HeizlastRaum::class)->orderBy('reihenfolge')->orderBy('id');
    }

    /**
     * @return HasMany<SanierungsVariante, $this>
     */
    public function varianten(): HasMany
    {
        return $this->hasMany(SanierungsVariante::class)->orderBy('reihenfolge')->orderBy('id');
    }
}
