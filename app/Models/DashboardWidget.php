<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DashboardWidget extends Model
{
    protected $fillable = [
        'key',
        'title',
        'subtitle',
        'icon',
        'color',
        'default_view',
        'tags',
        'default_col_span',
        'default_row_span',
        'component',
        'allow_multiple',
        'is_active',
        'sort_order',
        'meta',
    ];

    protected $casts = [
        'tags' => 'array',
        'meta' => 'array',
        'allow_multiple' => 'boolean',
        'is_active' => 'boolean',
        'default_col_span' => 'integer',
        'default_row_span' => 'integer',
        'sort_order' => 'integer',
    ];

    public function userWidgets()
    {
        return $this->hasMany(UserDashboardWidget::class);
    }
}