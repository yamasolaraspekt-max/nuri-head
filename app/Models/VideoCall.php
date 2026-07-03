<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoCall extends Model
{
    public const STATUS_CREATED = 'created';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_ENDED = 'ended';

    protected $fillable = [
        'customer_id',
        'peer_user_id',
        'chat_group_id',
        'created_by',
        'room_name',
        'status',
        'started_at',
        'ended_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    /** Kunde = new_leads (F4). */
    public function customer()
    {
        return $this->belongsTo(NewLeads::class, 'customer_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** Interner 1:1-Call: das angerufene Gegenüber. */
    public function peer()
    {
        return $this->belongsTo(User::class, 'peer_user_id');
    }

    /** Interner Gruppen-Call: die Chat-Gruppe. */
    public function chatGroup()
    {
        return $this->belongsTo(ChatGroup::class, 'chat_group_id');
    }

    public function invites()
    {
        return $this->hasMany(VideoCallInvite::class);
    }

    /** Interner Call (kein Kunde) = kein Gast-Zugang, nicht im Kundenprofil. */
    public function isInternal(): bool
    {
        return $this->customer_id === null;
    }

    /** Nur created/active dürfen beigetreten werden (Gast-Status-Check, F5). */
    public function isJoinable(): bool
    {
        return in_array($this->status, [self::STATUS_CREATED, self::STATUS_ACTIVE], true);
    }

    public function durationSeconds(): ?int
    {
        if ($this->started_at && $this->ended_at) {
            return $this->started_at->diffInSeconds($this->ended_at);
        }

        return null;
    }

    /** Menschliche Dauer, z. B. "12 Min. 3 Sek." — für die Chat-Beendet-Nachricht. */
    public function durationHuman(): ?string
    {
        $seconds = $this->durationSeconds();
        if ($seconds === null) {
            return null;
        }

        $minutes = intdiv($seconds, 60);
        $rest = $seconds % 60;

        return $minutes > 0 ? "{$minutes} Min. {$rest} Sek." : "{$rest} Sek.";
    }
}
