<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferTemplateFavorite extends Model
{
    // 1. Explicitly define the table name just to be safe
    protected $table = 'offer_template_favorites';

    // 2. Allow mass assignment for these columns
    protected $fillable = [
        'offer_template_id',
        'employee_id',
    ];

    // 3. Relationship back to the OfferTemplate
    public function offerTemplate()
    {
        return $this->belongsTo(OfferTemplate::class, 'offer_template_id');
    }

    // 4. Relationship back to the Employee who favorited it
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}