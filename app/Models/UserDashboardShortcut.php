<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDashboardShortcut extends Model
{
    protected $fillable = [
        'user_id',
        'employee_id',
        'label',
        'subtitle',
        'icon',
        'url',
        'permission_key',
        'permission_action',
        'is_visible',
        'sort_order',
        'meta',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'sort_order' => 'integer',
        'meta' => 'array',
    ];
}