<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Physik-/Referenzschicht: wiederverwendbarer Bauteilaufbau (Schichten referenzieren
 * materials). Der U-Wert wird über die Schicht-Engine (DIN EN ISO 6946) ermittelt und
 * in u_wert_berechnet gecacht.
 *
 * Transplantat aus wberechnung. `typ` wird auf App\Enums\KonstruktionTyp gecastet (Enum
 * nachgezogen); UWertService ist portiert und liefert bauteiltyp()-basierte Rsi/Rse-Werte.
 * Die zugehörige Tabelle `konstruktionen` existiert in ticket bereits.
 */
class Konstruktion extends Model
{
    protected $table = 'konstruktionen';

    /** @var list<string> */
    protected $fillable = ['name', 'typ', 'schichten', 'u_wert_berechnet', 'quelle', 'ist_vorlage'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'typ' => \App\Enums\KonstruktionTyp::class,
            'schichten' => 'array',
            'u_wert_berechnet' => 'float',
            'ist_vorlage' => 'boolean',
        ];
    }
}
