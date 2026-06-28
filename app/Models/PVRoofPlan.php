<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PVRoofPlan extends Model
{
    use HasFactory; 

    protected $fillable = [
        'product_id',
        'roof_id',
        'roof_structures',
        'planned_action',
        'planned_note',
    ];

    /**
     * Get the product that owns the roof plan.
     */
    public function product()
    {
        return $this->belongsTo(ArticleGroup::class, 'product_id');
    }

    /**
     * Get the roof (checklist) that is associated with the roof plan.
     */
    public function roof()
    {
        return $this->belongsTo(PVLongChecklist::class, 'roof_id');
    }
}
