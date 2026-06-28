<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadEmail extends Model
{
    protected $fillable = [
        'message_id',
        'from',
        'domain',
        'subject',
        'body',
        'date',
        'is_read',
        'status',
    ];

    protected $casts = [
        'date' => 'datetime',
        'is_read' => 'boolean',
    ];

    public function getFromNameAttribute(): ?string
    {
        $from = $this->from ?? '';

        if (preg_match('/^(.*?)\s*<.*>$/', $from, $matches)) {
            return trim($matches[1], "\"' ");
        }

        return null;
    }

    public function getFromEmailAttribute(): ?string
    {
        $from = $this->from ?? '';

        if (preg_match('/<([^>]+)>/', $from, $matches)) {
            return strtolower(trim($matches[1]));
        }

        if (filter_var($from, FILTER_VALIDATE_EMAIL)) {
            return strtolower(trim($from));
        }

        return null;
    }
}