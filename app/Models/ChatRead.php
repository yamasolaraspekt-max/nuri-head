<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatRead extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'chat_reads';

    protected $fillable = [
        'chat_id',
        'user_id',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function chat()
    {
        return $this->belongsTo(Chat::class, 'chat_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
