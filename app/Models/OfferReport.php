<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferReport extends Model
{
    use HasFactory;
    
    public $fillable = [
               'subject_id',
               'customer_id',
            'ending_id',
            'cover_id',
            'master_set_id',
            'article_group',
            'sub_article',
    ];
}
