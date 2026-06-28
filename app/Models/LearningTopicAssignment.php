<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LearningTopicAssignment extends Model
{
    protected $fillable = [
        'learning_topic_id',
        'assignable_type',
        'assignable_id',
    ];

    public function topic()
    {
        return $this->belongsTo(LearningTopic::class, 'learning_topic_id');
    }

    public function assignable()
    {
        return $this->morphTo();
    }
}
