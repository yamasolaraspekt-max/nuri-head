<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checklist extends Model
{
    use HasFactory;
    protected $fillable = [
    'product_id',
    'mount_plan_start',
    'mount_plan_end',
    'total_days',
    'article_group',
    'checklist_title',
    'direct_delivery_date',
    'direct_delivery_time',
    'direct_delivery_inner_unit',
    'direct_delivery_outer_unit',
    'direct_delivery_buffer_storage',
    'crane_hub_date',
    'crane_hub_time',
    'crane_hub_inner_unit',
    'crane_hub_outer_unit',
    'crane_hub_buffer_storage',
    'old_system_oil_heating',
    'old_system_gas_heating',
    'old_system_night_storage',
    'old_system_old_heat_pump'
];

public function articleGroup() {
    return $this->belongsTo(ArticleGroup::class, 'article_group');
}

public function product() {
    return $this->belongsTo(Product::class);
}

}
