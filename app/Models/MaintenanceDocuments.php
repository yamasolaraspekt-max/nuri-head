<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceDocument extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'documentable_id',
        'documentable_type',
        'title',
        'description',
        'category',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
        'uploaded_by',
        'uploaded_at',
        'meta',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
        'meta'        => 'array',
    ];

    public function documentable()
    {
        return $this->morphTo();
    }

    public function uploader()
    {
        return $this->belongsTo(Employee::class, 'uploaded_by');
    }
}
