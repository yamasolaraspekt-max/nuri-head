<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoCallInvite extends Model
{
    protected $fillable = [
        'video_call_id',
        'name',
        'email',
        'sent_at',
        'created_by',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function videoCall()
    {
        return $this->belongsTo(VideoCall::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
