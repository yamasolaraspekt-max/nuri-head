<?php

 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BreakingNews extends Model
{
    protected $table = 'breaking_news';

    protected $fillable = [
        'title',
        'message',
        'type',
        'icon',
        'is_active',
        'starts_at',
        'ends_at',
        'created_by',
        'audio_path'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(\App\Models\Employee::class, 'created_by');
    }

     // convenience accessor for full audio URL
    protected $appends = ['audio_url'];

    public function getAudioUrlAttribute()
    {
        return $this->audio_path
            ? asset('storage/'.$this->audio_path)
            : null;
    }
}
