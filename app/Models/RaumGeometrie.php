<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Grundriss-Geometrie eines Heizlast-Raums (Polygon + Wandsegmente). Quelle für die
 * Flächenableitung (GeometrieAbleitungService); kein Engine-Bestandteil. Mirror aus wberechnung.
 */
class RaumGeometrie extends Model
{
    protected $table = 'raum_geometrien';

    /** @var list<string> */
    protected $fillable = ['heizlast_raum_id', 'geschoss', 'polygon', 'hoehe_mm', 'wand_segmente', 'decke', 'boden'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'geschoss' => 'integer',
            'hoehe_mm' => 'integer',
            'polygon' => 'array',
            'wand_segmente' => 'array',
            'decke' => 'array',
            'boden' => 'array',
        ];
    }

    /** @return BelongsTo<HeizlastRaum, $this> */
    public function heizlastRaum(): BelongsTo
    {
        return $this->belongsTo(HeizlastRaum::class);
    }
}
