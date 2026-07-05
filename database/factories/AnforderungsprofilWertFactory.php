<?php

namespace Database\Factories;

use App\Models\AnforderungsprofilWert;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AnforderungsprofilWert>
 */
class AnforderungsprofilWertFactory extends Factory
{
    protected $model = AnforderungsprofilWert::class;

    public function definition(): array
    {
        return [
            'schluessel' => 'phi_hl_kw',
            'wert' => '9.6',
            'wert_num' => 9.6,
            'einheit' => 'kW',
            'datenlage' => 'berechnet',
            'quelle' => 'HeizlastRechner',
            'erfassungsweg' => 'berechnet',
        ];
    }
}
