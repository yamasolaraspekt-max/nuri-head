<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiMessage extends Model
{
    use HasFactory;

    protected $fillable = ['ai_chat_id','user_id','role','content','meta'];

    protected $casts = [ 'meta' => 'array' , 'embedding' => 'array', ];
 

    public function chat(){ return $this->belongsTo(AiChat::class, 'ai_chat_id'); }
    public function user(){ return $this->belongsTo(User::class); }
}