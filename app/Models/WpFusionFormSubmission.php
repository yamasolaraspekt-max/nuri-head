<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WpFusionFormSubmission extends Model
{
    protected $fillable = [
        'id', 'form_id', 'time', 'source_url', 'post_id', 'user_id',
        'user_agent', 'ip', 'is_read', 'privacy_scrub_date',
        'on_privacy_scrub', 'data'
    ];
}
