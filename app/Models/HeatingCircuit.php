<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/** Verteilsystem/Heizkreis je Kunde/Objekt. Struktur; Einrohr-Kaskade (B6) am Cut-over im Service. */
class HeatingCircuit extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'alternative_id',
        'name',
        'typ',
        'ziel_vorlauf_c',
        'spreizung_k',
        'reihenfolge',
        'meta',
    ];

    protected $casts = [
        'ziel_vorlauf_c' => 'decimal:1',
        'spreizung_k' => 'decimal:1',
        'reihenfolge' => 'integer',
        'meta' => 'array',
    ];

    public function customer()
    {
        return $this->belongsTo(NewLeads::class, 'customer_id', 'id');
    }

    public function alternative()
    {
        return $this->belongsTo(LeadAlternativeAdd::class, 'alternative_id', 'id');
    }

    public function radiators()
    {
        return $this->hasMany(RadiatorInstallation::class, 'heating_circuit_id', 'id');
    }
}
