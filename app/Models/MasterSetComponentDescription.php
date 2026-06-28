<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterSetComponentDescription extends Model
{
  protected $fillable = [
    'master_set_component_id',
    'context',
    'title',
    'sort_order',
    'delta',
    'html',
    'text',
  ];

  protected $casts = [
    'delta' => 'array',
  ];

  public function component()
  {
    return $this->belongsTo(MasterSetComponent::class, 'master_set_component_id');
  }
}
