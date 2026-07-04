<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/** Anschlussart-Korrekturfaktoren (B5). Reine Konfig-Stammdaten; Anwendung im Adapter (Cut-over). */
class RadiatorConnectionFactor extends Model
{
    use HasFactory;

    protected $fillable = [
        'anschluss_position',
        'anschluss_fuehrung',
        'bauart',
        'faktor',
        'quelle',
        'note',
    ];

    protected $casts = [
        'faktor' => 'decimal:3',
    ];
}
