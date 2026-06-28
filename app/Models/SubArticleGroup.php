<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubArticleGroup extends Model
{
    use HasFactory,softDeletes;

    protected $fillable = ['article_group_id', 'sub_article', 'initial', 'value', 'status'];

    public function articleGroup()
    {
        return $this->belongsTo(ArticleGroup::class);
    }

    public function masterSets()
    {
        return $this->hasMany(\App\Models\ProductMasterSet::class, 'sub_article');
    }

 


}
