<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    protected $fillable = [
        'user_id', 
        'default_tab', 
        'show_thumbnails', 
        'show_calc_sidebar', 
        'list_columns'
    ];

    protected $casts = [
        'show_thumbnails' => 'boolean',
        'show_calc_sidebar' => 'boolean',
        'list_columns' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}