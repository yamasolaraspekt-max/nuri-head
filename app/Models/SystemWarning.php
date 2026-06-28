<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SystemWarning extends Model
{
    protected $fillable = [
        'is_active',
        'type',
        'title',
        'message',
        'button_text',
        'theme',
        'can_close',
        'show_backdrop',
        'started_at',
        'ended_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'can_close' => 'boolean',
        'show_backdrop' => 'boolean',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function histories(): HasMany
    {
        return $this->hasMany(SystemWarningHistory::class);
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate(
            ['id' => 1],
            [
                'is_active' => false,
                'type' => 'development',
                'title' => 'Kurze Wartung',
                'message' => 'Wir führen gerade eine technische Änderung durch. Bitte speichern Sie Ihre Arbeit.',
                'button_text' => 'Verstanden',
                'theme' => 'amber',
                'can_close' => true,
                'show_backdrop' => true,
            ]
        );
    }

    public function publicPayload(): array
    {
        return [
            'id' => $this->id,
            'is_active' => $this->is_active,
            'type' => $this->type,
            'title' => $this->title,
            'message' => $this->message,
            'button_text' => $this->button_text,
            'theme' => $this->theme,
            'can_close' => $this->can_close,
            'show_backdrop' => $this->show_backdrop,
            'started_at' => optional($this->started_at)->toIso8601String(),
            'ended_at' => optional($this->ended_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}