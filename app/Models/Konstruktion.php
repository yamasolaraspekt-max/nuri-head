<?php

namespace App\Models;

use App\Enums\KonstruktionTyp;
use App\Services\Heizlast\UWertService;
use Illuminate\Database\Eloquent\Model;

/**
 * Physik-/Referenzschicht: wiederverwendbarer Bauteilaufbau (Schichten referenzieren
 * materials). Der U-Wert wird ausschließlich über UWertService::ausSchichten()
 * (DIN EN ISO 6946) ermittelt und in u_wert_berechnet gecacht.
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
            'typ' => KonstruktionTyp::class,
            'schichten' => 'array',
            'u_wert_berechnet' => 'float',
            'ist_vorlage' => 'boolean',
        ];
    }

    /**
     * Rechnet den U-Wert über die bestehende Schicht-Engine neu, speichert ihn in
     * u_wert_berechnet (falls persistiert) und gibt ihn zurück.
     */
    public function recomputeUWert(UWertService $uWertService): float
    {
        $u = (float) $uWertService->ausKonstruktion($this)['u_wert'];
        $this->u_wert_berechnet = $u;
        if ($this->exists) {
            $this->save();
        }

        return $u;
    }
}
