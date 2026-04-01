<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AiChat extends Model
{
    use HasFactory;

        protected $fillable = [
        'user_id','customer_id','title','last_activity_at',
        'memory_summary','memory_updated_at',
        ];

    protected $casts = [
        'is_shared' => 'boolean',
        'last_activity_at' => 'datetime',
    ];

    public function user()     { return $this->belongsTo(User::class); }
    public function customer() { return $this->belongsTo(NewLeads::class, 'customer_id'); }
    public function messages() { return $this->hasMany(AiMessage::class); }
}
