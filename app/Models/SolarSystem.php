<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolarSystem extends Model
{
    protected $fillable = [
        'lead_id', 'alternative_id', 'product_id', 'kwp_size', 'self_consumption_rate', 'feed_in_tariff',
        'system_price', 'battery_capacity', 'battery_price'
    ];

    public function lead()
    {
        return $this->belongsTo(NewLeads::class);
    }
}
