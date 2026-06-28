<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DealMeasurementDetail extends Model
{
    protected $fillable = [
        'deal_measurement_id',
        'deal_id',
        'offer_id',
        'offer_detail_id',
        'type',
        'form_data',
        'roof_data',
        'pv_data',
        'wp_data',
        'raw_snapshot',
        'updated_by',
        'saved_at',
    ];

    protected $casts = [
        'form_data'    => 'array',
        'roof_data'    => 'array',
        'pv_data'      => 'array',
        'wp_data'      => 'array',
        'raw_snapshot' => 'array',
        'saved_at'     => 'datetime',
    ];

    public function measurement()
    {
        return $this->belongsTo(DealMeasurement::class, 'deal_measurement_id');
    }

    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    public function offerDetail()
    {
        return $this->belongsTo(OfferDetail::class);
    }
}