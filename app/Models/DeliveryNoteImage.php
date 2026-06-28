<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryNoteImage extends Model
{
    protected $fillable = [
        'delivery_note_id',
        'name',
        'image',
    ];

    public function deliveryNote(): BelongsTo
    {
        return $this->belongsTo(DeliveryNote::class, 'delivery_note_id');
    }
}