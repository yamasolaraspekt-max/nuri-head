<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlannerItemMaterial extends Model
{
    use HasFactory;

    protected $table = 'planner_item_materials';

    protected $fillable = [
        'planner_item_id',
        'master_set_id',
        'name',
        'qty',
        'unit_price',
        'total_price',
    ];

    /**
     * The Planner Item (Task/Card) this material belongs to.
     */
    public function plannerItem()
    {
        return $this->belongsTo(PlannerItem::class);
    }

    /**
     * The original Master Set definition (optional link).
     */
    public function masterSet()
    {
        return $this->belongsTo(MasterSet::class);
    }
}