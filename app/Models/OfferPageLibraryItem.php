<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OfferPageLibraryItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'article_group_id',
        'product_id',
        'created_by',
        'title',
        'description',
        'file_disk',
        'file_path',
        'file_url',
        'original_name',
        'mime_type',
        'size_bytes',
        'width',
        'height',
        'is_active',
        'sort_order',
        'meta',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'meta' => 'array',
        'size_bytes' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'article_group_id' => 'integer',
        'product_id' => 'integer',
        'created_by' => 'integer',
    ];

    public function articleGroup(): BelongsTo
    {
        return $this->belongsTo(ArticleGroup::class, 'article_group_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }
}
