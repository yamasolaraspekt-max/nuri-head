<?php

// app/Models/CostingSetRole.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CostingSetRole extends Model
{
    protected $fillable = [
        'costing_set_id','qualification_id',
        'wage_cost_per_hour','payroll_overhead_percent','company_overhead_percent',
        'full_cost_rate_per_hour','sell_rate_per_hour',
        'sort_order','status',
    ];

    public function costingSet(): BelongsTo
    {
        return $this->belongsTo(CostingSet::class);
    }

    public function qualification(): BelongsTo
    {
        return $this->belongsTo(PositionQualification::class, 'qualification_id');
    }

    // Optional helper (if you want to compute instead of storing)
    public function computeFullCost(): float
    {
        $w = (float) $this->wage_cost_per_hour;
        $p = (float) $this->payroll_overhead_percent / 100;
        $o = (float) $this->company_overhead_percent / 100;
        return round($w * (1 + $p + $o), 4);
    }
}