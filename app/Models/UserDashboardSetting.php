<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDashboardSetting extends Model
{
    protected $fillable = [
        'user_id',
        'employee_id',
        'active_view',
        'theme',
        'density',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];
}