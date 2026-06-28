<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LearningTopic extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'prompt_label',
        'short_intro',
        'body',
        'estimated_minutes',
        'difficulty',
        'audience_scope',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function media()
    {
        return $this->hasMany(LearningTopicMedia::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function assignments()
    {
        return $this->hasMany(LearningTopicAssignment::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
