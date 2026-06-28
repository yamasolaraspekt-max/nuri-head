<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgeQuestion extends Model
{
    protected $fillable = [
        'question', 'description', 'video', 'created_by', 'knowledge_id'
    ];
}
