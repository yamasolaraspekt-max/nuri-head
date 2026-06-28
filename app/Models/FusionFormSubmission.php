<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FusionFormSubmission extends Model
{
    protected $table = 'wp_fusion_form_submissions';

    protected $fillable = [
        'id',
        'form_id',
        'time',
        'source_url',
        'post_id',
        'user_id',
        'user_agent',
        'ip',
        'is_read',
        'privacy_scrub_date',
        'on_privacy_scrub',
        'data',
    ];

   protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'time' => 'datetime',
        'privacy_scrub_date' => 'date',
    ];


    // Optional relationships
    public function form()
    {
        return $this->belongsTo(WpFusionForm::class, 'form_id', 'form_id');
    }

    public function entries()
    {
        return $this->hasMany(WpFusionFormEntry::class, 'submission_id', 'id');
    }
}
