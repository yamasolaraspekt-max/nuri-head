<?php

// app/Models/MasterSetGroup.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterSetGroup extends Model
{
  use SoftDeletes;

  protected $fillable = [
    'article_group_id',
    'name',
    'description',
    'color',
  ];

  public function articleGroup() {
    return $this->belongsTo(ArticleGroup::class, 'article_group_id');
  }


    public function masterSets()
    {
        // pivot table name must match your DB
        return $this->belongsToMany(
            MasterSet::class,
            'master_set_group_master_set',   // <-- your pivot table
            'master_set_group_id',
            'master_set_id'
        );
    }
}
