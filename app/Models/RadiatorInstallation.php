<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RadiatorInstallation extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'alternative_id',
        'postcode',
        'address_no',
        'number',
        'type',
        'radiator_type',
        'floor',
        'room',
        'room_size',
        'width',
        'height',
        'depth',
        'niche_top',
        'niche_bottom',
        'niche_left',
        'niche_right',
        'has_window_sill',
        'supply_valve',
        'supply_valve_presettable',
        'return_valve',
        'return_valve_present',
        'design',
        'renew_thermostat_head',
        'has_socket',
        'socket_distance',
        'limbs',
        'image',
        // Heizkörper-Modul (Stufe i/ii):
        'radiator_spec_id',
        'anzahl',
        'anschluss_position',
        'anschluss_fuehrung',
        'ventil_einsatz_bestand',
        'kopf_norm_bestand',
        'heating_circuit_id',
        'q_norm_w_pro_m_override',
        'exponent_n_override',
        'typ_konfidenz',
    ];

    protected $casts = [
        'has_window_sill' => 'boolean',
        'supply_valve_presettable' => 'boolean',
        'return_valve_present' => 'boolean',
        'renew_thermostat_head' => 'boolean',
        'has_socket' => 'boolean',
        'room_size' => 'decimal:2',
        'socket_distance' => 'decimal:2',
        'anzahl' => 'integer',
        'q_norm_w_pro_m_override' => 'decimal:2',
        'exponent_n_override' => 'decimal:2',
    ];

    protected $appends = ['image_url'];

    public function customer()
    {
        return $this->belongsTo(NewLeads::class, 'customer_id', 'id');
    }

    public function alternative()
    {
        return $this->belongsTo(LeadAlternativeAdd::class, 'alternative_id', 'id');
    }

    public function spec()
    {
        return $this->belongsTo(RadiatorSpec::class, 'radiator_spec_id', 'id');
    }

    public function heatingCircuit()
    {
        return $this->belongsTo(HeatingCircuit::class, 'heating_circuit_id', 'id');
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('images/radiators/' . $this->image) : null;
    }
}