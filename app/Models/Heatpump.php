<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Heatpump extends Model
{
    protected $fillable = [
       'lead_id', 'alternative_id', 'product_id', 'type', 'cop', 'installation_cost', 'annual_costs'
    ];

    public function lead()
    {
        return $this->belongsTo(NewLead::class);
    }
}
