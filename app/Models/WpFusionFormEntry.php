<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WpFusionFormEntry extends Model
{
    protected $fillable = [
        'submission_id', 'form_id', 'field_id', 'value', 'privacy', 'data'
    ];
}

