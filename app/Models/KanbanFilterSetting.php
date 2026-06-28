<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KanbanFilterSetting extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'employee_id',
        'name',
        'filters',
        'is_default',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'employee_id' => 'integer',
        'filters' => 'array',
        'is_default' => 'boolean',
    ];

    public function scopeForCurrentUser($query)
    {
        return $query->where('user_id', auth()->id());
    }
}
