<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfferFolder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'offer_id',
        'customer_id',
        'alternative_id',
        'product_id',
        'created_by',
        'name',
        'color',
        'history',
        'status',
        'document_status',
        'offer_status',
        'deal_status',
    ];

    protected $casts = [
        'history' => 'array',
    ];

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(NewLeads::class, 'customer_id');
    }

    public function alternative(): BelongsTo
    {
        return $this->belongsTo(LeadAlternativeAdd::class, 'alternative_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(ArticleGroup::class, 'product_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

    public function attachments()
    {
        return $this->hasMany(\App\Models\OfferFolderAttachment::class, 'offer_folder_id')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

  public function getOfferStatusLabelAttribute(): string
    {
        return match (strtolower($this->offer_status ?? self::OFFER_STATUS_DRAFT)) {
            self::OFFER_STATUS_DRAFT => 'Entwurf',
            self::OFFER_STATUS_PENDING_APPROVAL => 'Wartet auf Freigabe',
            self::OFFER_STATUS_SENT => 'Gesendet',
            self::OFFER_STATUS_VIEWED => 'Gesehen',
            self::OFFER_STATUS_NEGOTIATION => 'Verhandlung',
            self::OFFER_STATUS_REVISED => 'Überarbeitet',
            self::OFFER_STATUS_ACCEPTED => 'Akzeptiert',
            self::OFFER_STATUS_REJECTED => 'Abgelehnt',
            self::OFFER_STATUS_EXPIRED => 'Abgelaufen',
            self::OFFER_STATUS_CANCELLED => 'Storniert',
            default => 'Entwurf',
        };
    }

    public function getDealStatusLabelAttribute(): string
    {
        return match (strtolower($this->deal_status ?? self::DEAL_STATUS_OPEN)) {
            self::DEAL_STATUS_OPEN => 'Offen',
            self::DEAL_STATUS_QUALIFIED => 'Qualifiziert',
            self::DEAL_STATUS_PROPOSAL => 'Angebotsphase',
            self::DEAL_STATUS_NEGOTIATION => 'Verhandlung',
            self::DEAL_STATUS_WON => 'Gewonnen',
            self::DEAL_STATUS_LOST => 'Verloren',
            self::DEAL_STATUS_ON_HOLD => 'Pausiert',
            default => 'Offen',
        };
    }


    public function getWorkflowStatusAttribute(): string
    {
        return $this->document_status === self::DOCUMENT_STATUS_DEAL
            ? (string) ($this->deal_status ?: self::DEAL_STATUS_OPEN)
            : (string) ($this->offer_status ?: self::OFFER_STATUS_DRAFT);
    }

    public function getWorkflowStatusLabelAttribute(): string
    {
        return $this->document_status === self::DOCUMENT_STATUS_DEAL
            ? $this->deal_status_label
            : $this->offer_status_label;
    }

    public function getWorkflowStatusBadgeClassAttribute(): string
    {
        if ($this->document_status === self::DOCUMENT_STATUS_DEAL) {
            return match (strtolower($this->deal_status ?? self::DEAL_STATUS_OPEN)) {
                self::DEAL_STATUS_OPEN => 'secondary',
                self::DEAL_STATUS_QUALIFIED => 'info',
                self::DEAL_STATUS_PROPOSAL => 'primary',
                self::DEAL_STATUS_NEGOTIATION => 'warning',
                self::DEAL_STATUS_WON => 'success',
                self::DEAL_STATUS_LOST => 'danger',
                self::DEAL_STATUS_ON_HOLD => 'dark',
                default => 'secondary',
            };
        }

        return match (strtolower($this->offer_status ?? self::OFFER_STATUS_DRAFT)) {
            self::OFFER_STATUS_DRAFT => 'secondary',
            self::OFFER_STATUS_PENDING_APPROVAL => 'warning',
            self::OFFER_STATUS_SENT => 'info',
            self::OFFER_STATUS_VIEWED => 'primary',
            self::OFFER_STATUS_NEGOTIATION => 'warning',
            self::OFFER_STATUS_REVISED => 'primary',
            self::OFFER_STATUS_ACCEPTED => 'success',
            self::OFFER_STATUS_REJECTED => 'danger',
            self::OFFER_STATUS_EXPIRED => 'dark',
            self::OFFER_STATUS_CANCELLED => 'danger',
            default => 'secondary',
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match (strtolower($this->status ?? 'draft')) {
            'sent'        => 'info',
            'negotiation' => 'warning',
            'final'       => 'success',
            'cancel'      => 'danger',
            default       => 'secondary',
        };
    }

    public function detail()
    {
        return $this->hasOne(OfferDetail::class, 'offer_folder_id');
    }

    public function offerProductLists()
    {
        return $this->hasMany(OfferProductList::class, 'offer_folder_id');
    }

    public function productLists()
    {
        return $this->hasMany(\App\Models\OfferProductList::class, 'offer_folder_id');
    }

    public function assetLists()
    {
        return $this->hasMany(OfferAssetList::class, 'offer_folder_id');
    }

    public function pdfPrints()
    {
        return $this->hasMany(OfferPdfPrint::class, 'offer_folder_id')->orderByDesc('created_at');
    }

    public const DOCUMENT_STATUS_OFFER = 'offer';
    public const DOCUMENT_STATUS_DEAL = 'deal';

    public const OFFER_STATUS_DRAFT = 'draft';
    public const OFFER_STATUS_PENDING_APPROVAL = 'pending_approval';
    public const OFFER_STATUS_SENT = 'sent';
    public const OFFER_STATUS_VIEWED = 'viewed';
    public const OFFER_STATUS_NEGOTIATION = 'negotiation';
    public const OFFER_STATUS_REVISED = 'revised';
    public const OFFER_STATUS_ACCEPTED = 'accepted';
    public const OFFER_STATUS_REJECTED = 'rejected';
    public const OFFER_STATUS_EXPIRED = 'expired';
    public const OFFER_STATUS_CANCELLED = 'cancelled';

    public const DEAL_STATUS_OPEN = 'open';
    public const DEAL_STATUS_QUALIFIED = 'qualified';
    public const DEAL_STATUS_PROPOSAL = 'proposal';
    public const DEAL_STATUS_NEGOTIATION = 'negotiation';
    public const DEAL_STATUS_WON = 'won';
    public const DEAL_STATUS_LOST = 'lost';
    public const DEAL_STATUS_ON_HOLD = 'on_hold';

    public const OFFER_STATUSES = [
        self::OFFER_STATUS_DRAFT,
        self::OFFER_STATUS_PENDING_APPROVAL,
        self::OFFER_STATUS_SENT,
        self::OFFER_STATUS_VIEWED,
        self::OFFER_STATUS_NEGOTIATION,
        self::OFFER_STATUS_REVISED,
        self::OFFER_STATUS_ACCEPTED,
        self::OFFER_STATUS_REJECTED,
        self::OFFER_STATUS_EXPIRED,
        self::OFFER_STATUS_CANCELLED,
    ];

    public const DEAL_STATUSES = [
        self::DEAL_STATUS_OPEN,
        self::DEAL_STATUS_QUALIFIED,
        self::DEAL_STATUS_PROPOSAL,
        self::DEAL_STATUS_NEGOTIATION,
        self::DEAL_STATUS_WON,
        self::DEAL_STATUS_LOST,
        self::DEAL_STATUS_ON_HOLD,
    ];

    public function libraryPages()
    {
        return $this->hasMany(\App\Models\OfferFolderLibraryPage::class, 'offer_folder_id')
            ->orderBy('page_position')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

}