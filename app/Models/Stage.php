<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stage extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'stage',
        'product_id',
        'phase_section_id',  
        'version',
        'status',
        'sort_order',
        'default',
    ];

    protected $casts = [
        'product_id'       => 'integer',
        'phase_section_id' => 'integer',
        'sort_order'       => 'integer',
    ];

    protected $attributes = [
        'status' => 'Published',
    ];

    public function product()
    {
        return $this->belongsTo(\App\Models\ArticleGroup::class, 'product_id');
    }

    public function section()
    {
        return $this->belongsTo(\App\Models\PhaseSection::class, 'phase_section_id');
    }

    public function phases()
    {
        return $this->hasMany(\App\Models\TaskPhase::class, 'stage_id', 'id')->orderBy('sort_order');
    }
}
