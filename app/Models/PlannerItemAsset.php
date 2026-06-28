<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlannerItemAsset extends Model
{
    protected $table = 'planner_item_assets';

    protected $fillable = ['planner_item_id','asset_id','qty','notes'];

    public function item()
    {
        return $this->belongsTo(PlannerItem::class, 'planner_item_id');
    }
}
