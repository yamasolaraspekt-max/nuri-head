<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Förderprogramm (KfW/BAFA/Land/Kommune/EU) mit Antragsstatus-Workflow,
 * Frist und Förderbetrag. Soft-Archiv via `archived_at` (kein Hard-Delete).
 *
 * Übernommen aus playground, angepasst an ticket. Ersetzt fachlich das
 * kaputte BEG-Förderungen-Modul (Standalone-Liste).
 */
class Foerderung extends Model
{
    protected $table = 'foerderungen';

    protected $fillable = [
        'antragsnummer',
        'bezeichnung',
        'foerdertyp',
        'betrag',
        'antragsstatus',
        'deadline',
        'projekt_ref',
        'notiz',
        'archived_at',
    ];

    protected $casts = [
        'betrag'      => 'decimal:2',
        'deadline'    => 'date',
        'archived_at' => 'datetime',
    ];
}
