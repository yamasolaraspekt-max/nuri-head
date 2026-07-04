<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/** Kompatibilität HK-Hersteller/Serie/Baujahr → Ventileinsatz → Kopf-Anschluss (+ optional Adapter). Stammdaten. */
class ValveInsertCompatibility extends Model
{
    use HasFactory;

    protected $table = 'valve_insert_compatibility';

    protected $fillable = [
        'hk_hersteller',
        'hk_serie',
        'baujahr_von',
        'baujahr_bis',
        'einsatz_accessory_id',
        'kopf_anschluss_norm',
        'adapter_accessory_id',
        'quelle',
        'note',
    ];

    protected $casts = [
        'baujahr_von' => 'integer',
        'baujahr_bis' => 'integer',
    ];

    public function einsatz()
    {
        return $this->belongsTo(Accessory::class, 'einsatz_accessory_id', 'id');
    }

    public function adapter()
    {
        return $this->belongsTo(Accessory::class, 'adapter_accessory_id', 'id');
    }
}
