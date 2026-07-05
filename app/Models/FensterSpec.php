<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Produktschicht: 1:1 zu artikel – technische Fenster-/Verglasungswerte (DIN EN ISO 10077).
 */
class FensterSpec extends Model
{
    /** @var list<string> */
    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'u_w' => 'float',
            'u_g' => 'float',
            'u_f' => 'float',
            'g_wert' => 'float',
            'psi_glasrand' => 'float',
        ];
    }

    /** @return BelongsTo<Artikel, $this> */
    public function artikel(): BelongsTo
    {
        return $this->belongsTo(Artikel::class);
    }
}
