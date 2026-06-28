<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterSetGroupSet extends Model
{
    protected $fillable = [
        'article_group_id',
        'name',
        'description',
        'color',
    ];

    public function masterSets()
    {
        return $this->belongsToMany(MasterSet::class, 'master_set_group_set_items')
            ->withPivot(['sort_order'])
            ->orderBy('master_set_group_set_items.sort_order');
    }
}
