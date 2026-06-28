<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityArticle extends Model
{
    protected $fillable = [
        'article_id', 
        'activity_id'
    ];
}
