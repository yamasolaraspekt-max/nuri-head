<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Baustoff/Dämmstoff mit Bemessungs-Wärmeleitfähigkeit λ (W/mK) für den
 * U-Wert-Schichtaufbau (Strategie B).
 */
class Material extends Model
{
    protected $fillable = ['name', 'kategorie', 'lambda_w_mk', 'rohdichte_kg_m3', 'quelle'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'lambda_w_mk' => 'float',
            'rohdichte_kg_m3' => 'integer',
        ];
    }
}
