<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferDetail extends Model
{
    public const DOCUMENT_STATUS_OFFER = 'offer';
    public const DOCUMENT_STATUS_DEAL = 'deal';
    public const DOCUMENT_STATUS_AUFTRAG = 'auftrag';

    protected $fillable = [
        'offer_id',
        'offer_folder_id',
        'offer_no',

        'branch_id',

        'document_status',
        'angebot_snapshot_sections',
        'angebot_snapshot_at',

        'brand_color',
        'brand_mode',
        'brand_logo_url',
        'company_name',
        'company_footer',
        'cover_text',
        'cover_text_html',

        'agb_title',
        'agb_text',

        'sections',
        'placed_images',
        'print_attachments',

        'total_net',
        'tax_rate',
        'total_gross',
        'biography_data',
        'material_history',
    ];

    protected $casts = [
        'sections' => 'array',
        'angebot_snapshot_sections' => 'array',
        'placed_images' => 'array',
        'print_attachments' => 'array',
        'company_footer' => 'array',
        'total_net' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'total_gross' => 'decimal:2',
        'biography_data' => 'array',
        'material_history' => 'array',
        'angebot_snapshot_at' => 'datetime',
    ];

    public function offer()
    {
        return $this->belongsTo(Offer::class, 'offer_id');
    }

    public function folder()
    {
        return $this->belongsTo(OfferFolder::class, 'offer_folder_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function isOfferPhase(): bool
    {
        return ($this->document_status ?? self::DOCUMENT_STATUS_OFFER) === self::DOCUMENT_STATUS_OFFER;
    }

    public function isExecutionPhase(): bool
    {
        return in_array(
            $this->document_status,
            [self::DOCUMENT_STATUS_DEAL, self::DOCUMENT_STATUS_AUFTRAG],
            true
        );
    }

    public function documentStatusLabel(): string
    {
        return match ($this->document_status) {
            self::DOCUMENT_STATUS_DEAL => 'Deal',
            self::DOCUMENT_STATUS_AUFTRAG => 'Auftrag',
            default => 'Angebot',
        };
    }

    public function measurement()
    {
        return $this->hasOne(\App\Models\DealMeasurement::class, 'offer_detail_id');
    }

    public function leadProductList()
    {
        return $this->hasOneThrough(
            \App\Models\LeadProductList::class,
            \App\Models\Offer::class,
            'id',
            'customer_id',
            'offer_id',
            'customer_id'
        );
    }

    public function getLeadProductListAttribute()
    {
        return \App\Models\LeadProductList::query()
            ->where('customer_id', $this->offer?->customer_id)
            ->where('product_id', $this->offer?->product_id)
            ->where('alternative_id', $this->offer?->alternative_id)
            ->first();
    }

    public function libraryPages()
    {
        return $this->hasMany(\App\Models\OfferFolderLibraryPage::class, 'offer_detail_id')
            ->orderBy('page_position')
            ->orderBy('sort_order')
            ->orderBy('id');
    }
}
