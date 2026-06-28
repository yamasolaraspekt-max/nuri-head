<?php

 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PinnedPrivateChat extends Model
{
    protected $fillable = ['user_id', 'other_user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function other()
    {
        return $this->belongsTo(User::class, 'other_user_id');
    }
}
