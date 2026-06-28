<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemWarningHistory extends Model
{
    protected $fillable = [
        'system_warning_id',
        'action',
        'type',
        'title',
        'message',
        'is_active',
        'changed_by',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function warning(): BelongsTo
    {
        return $this->belongsTo(SystemWarning::class, 'system_warning_id');
    }
}