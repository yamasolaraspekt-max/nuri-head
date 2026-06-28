<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CostingSet extends Model
{
    protected $fillable = [
        'name',
        'status',

        // Flags
        'is_default',
        'is_active',

        // AW config
        'aw_minutes',

        // Global markups / rules
        'material_overhead_percent',
        'labor_overhead_percent',
        'site_overhead_percent',
        'site_overhead_fixed',

        'risk_percent',
        'profit_percent',

        // Purchasing rules
        'small_parts_percent',
        'freight_fixed',
        'handling_fixed',
        'disposal_fixed',

        // Rounding rules
        'rounding_step',
        'rounding_mode',

        // Commission rule
        'commission_mode',
        'commission_value',

        // ✅ NEW: role defaults (auto-fill matrix)
        'default_payroll_overhead_percent',
        'default_company_overhead_percent',
        'default_sell_markup_percent',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active'  => 'boolean',

        // numeric casts (optional but useful)
        'aw_minutes' => 'integer',

        'material_overhead_percent' => 'float',
        'labor_overhead_percent'    => 'float',
        'site_overhead_percent'     => 'float',
        'site_overhead_fixed'       => 'float',

        'risk_percent'   => 'float',
        'profit_percent' => 'float',

        'small_parts_percent' => 'float',
        'freight_fixed'       => 'float',
        'handling_fixed'      => 'float',
        'disposal_fixed'      => 'float',

        'rounding_step' => 'float',
        'commission_value' => 'float',

        // ✅ NEW defaults
        'default_payroll_overhead_percent' => 'float',
        'default_company_overhead_percent' => 'float',
        'default_sell_markup_percent'      => 'float',
    ];

    public function roles(): HasMany
    {
        return $this->hasMany(CostingSetRole::class, 'costing_set_id');
    }
}