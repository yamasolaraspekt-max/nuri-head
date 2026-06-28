<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DealMeasurementHistory extends Model
{
    protected $fillable = [
        'deal_measurement_id',
        'action',
        'section',
        'field',
        'old_value',
        'new_value',
        'changes',
        'created_by',
        'created_by_user_id',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    public function measurement()
    {
        return $this->belongsTo(DealMeasurement::class, 'deal_measurement_id');
    }
}