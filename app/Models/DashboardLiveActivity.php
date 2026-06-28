<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DashboardLiveActivity extends Model
{
    protected $fillable = [
        'employee_id',
        'actor_user_id',
        'actor_employee_id',
        'type',
        'action',
        'subject_type',
        'subject_id',
        'title',
        'message',
        'url',
        'payload',
        'read_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'read_at' => 'datetime',
    ];

    public function getIsReadAttribute(): bool
    {
        return !is_null($this->read_at);
    }
}