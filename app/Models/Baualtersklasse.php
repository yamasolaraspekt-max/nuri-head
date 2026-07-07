<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Baualtersklasse mit typischen Bauteil-U-Werten je Epoche/Sanierungsstufe
 * (Strategie C – Schätzung, IWU/TABULA-Richtwerte). Mirror aus wberechnung;
 * Tabelle `baualtersklassen` existiert in ticket (Migration 2026_07_05_170003).
 */
class Baualtersklasse extends Model
{
    protected $table = 'baualtersklassen';

    protected $fillable = [
        'von_jahr', 'bis_jahr', 'sanierungsstufe',
        'u_wand', 'u_dach', 'u_boden', 'u_fenster', 'u_tuer', 'quelle',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'von_jahr' => 'integer',
            'bis_jahr' => 'integer',
            'u_wand' => 'float',
            'u_dach' => 'float',
            'u_boden' => 'float',
            'u_fenster' => 'float',
            'u_tuer' => 'float',
        ];
    }
}
