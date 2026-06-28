<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedbackImage extends Model
{
    use HasFactory;

    protected $fillable = ['feedback_id', 'title', 'image'];

    public function feedback()
    {
        return $this->belongsTo(Feedback::class);
    }
}
