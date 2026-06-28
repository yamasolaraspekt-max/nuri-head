<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDashboardWidget extends Model
{
    protected $fillable = [
        'user_id',
        'employee_id',
        'dashboard_widget_id',
        'widget_key',
        'instance_key',
        'view',
        'col_span',
        'row_span',
        'sort_order',
        'is_visible',
        'config',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'config' => 'array',
    ];

    public function widget()
    {
        return $this->belongsTo(DashboardWidget::class, 'dashboard_widget_id');
    }
}