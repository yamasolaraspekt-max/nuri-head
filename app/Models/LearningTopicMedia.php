<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LearningTopicMedia extends Model
{
    protected $fillable = [
        'learning_topic_id',
        'media_type',
        'title',
        'description',
        'file_path',
        'mime_type',
        'sort_order',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function topic()
    {
        return $this->belongsTo(LearningTopic::class, 'learning_topic_id');
    }
}
