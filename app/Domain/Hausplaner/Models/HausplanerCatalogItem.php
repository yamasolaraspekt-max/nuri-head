<?php

namespace App\Domain\Hausplaner\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Hausplaner P0 — Katalog-Item: NUR Planer-Zusätze (Maße, Darstellung, Platzierung,
 * Wartungsabstände/Schallrelevanz in technical_data). Herstellerdaten leben im
 * Spec-Standard; spec_ref ist der opake Verweis (▲K4 — Katalog-Reinheits-Test).
 */
class HausplanerCatalogItem extends Model
{
    protected $table = 'hausplaner_catalog_items';

    protected $fillable = [
        'category', 'manufacturer', 'model', 'dimensions', 'representation',
        'placement', 'spec_ref', 'technical_data', 'aktiv',
    ];

    protected $casts = [
        'dimensions' => 'array',
        'representation' => 'array',
        'placement' => 'array',
        'technical_data' => 'array',
        'aktiv' => 'boolean',
    ];
}
