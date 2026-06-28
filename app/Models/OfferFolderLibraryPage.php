<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OfferFolderLibraryPage extends Model
{
    use SoftDeletes;

    public const POSITION_AFTER_COVER = 'after_cover';
    public const POSITION_AFTER_ROOF = 'after_roof';
    public const POSITION_BEFORE_POSITIONS = 'before_positions';
    public const POSITION_AFTER_POSITIONS = 'after_positions';
    public const POSITION_BEFORE_FINAL = 'before_final';
    public const POSITION_END = 'end';

    public const POSITIONS = [
        self::POSITION_AFTER_COVER,
        self::POSITION_AFTER_ROOF,
        self::POSITION_BEFORE_POSITIONS,
        self::POSITION_AFTER_POSITIONS,
        self::POSITION_BEFORE_FINAL,
        self::POSITION_END,
    ];

    protected $fillable = [
        'offer_id',
        'offer_folder_id',
        'offer_detail_id',
        'offer_page_library_item_id',
        'article_group_id',
        'product_id',
        'created_by',
        'title',
        'file_url',
        'page_position',
        'sort_order',
        'is_enabled',
        'meta',
    ];

    protected $casts = [
        'offer_id' => 'integer',
        'offer_folder_id' => 'integer',
        'offer_detail_id' => 'integer',
        'offer_page_library_item_id' => 'integer',
        'article_group_id' => 'integer',
        'product_id' => 'integer',
        'created_by' => 'integer',
        'sort_order' => 'integer',
        'is_enabled' => 'boolean',
        'meta' => 'array',
    ];

    public function folder(): BelongsTo
    {
        return $this->belongsTo(OfferFolder::class, 'offer_folder_id');
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class, 'offer_id');
    }

    public function detail(): BelongsTo
    {
        return $this->belongsTo(OfferDetail::class, 'offer_detail_id');
    }

    public function libraryItem(): BelongsTo
    {
        return $this->belongsTo(OfferPageLibraryItem::class, 'offer_page_library_item_id');
    }

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
