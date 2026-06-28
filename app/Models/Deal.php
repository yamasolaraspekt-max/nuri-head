<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Deal extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'product_id',
        'alternative_id',
        'service_id',
        'department_id',
        'service',
        'employee_id',
        'offer_id',
        'offer_folder_id',
        'info',
        'price',
        'sign_date',
        'deal_status',
        'status',
        'measurement_status',  
        'status_msg',
        'project_status',
        'location',
        'order_number',
        'offer_number',
        'confirmed_at',
        'delivered_at',
        'checked_by',
        'reviewer_id',
    ];

    protected static function booted(): void
    {
        static::creating(function (Deal $deal) {
            if (blank($deal->order_number)) {
                $deal->order_number = static::generateOrderNo(
                    $deal->customer_id,
                    $deal->department_id
                );
            }
        });
    }

    public static function generateOrderNo(?int $customerId = null, ?int $departmentId = null): string
    {
        return DB::transaction(function () use ($customerId, $departmentId) {
            // Reuse the branch logic from the Offer model
            $branchPrefix = \App\Models\Offer::resolveOfferPrefix($customerId, $departmentId);

            $year = date('y');

            // Base prefix for Deals (e.g., 'SA-AB26')
            $basePrefix = "{$branchPrefix}-AB{$year}";

            $lastDeal = static::query()
                ->where('order_number', 'like', $basePrefix . '%')
                ->lockForUpdate()
                ->orderByDesc('id')
                ->first();

            $nextNumber = 1;

            if ($lastDeal && preg_match('/^' . preg_quote($basePrefix, '/') . '(\d{3,})$/', $lastDeal->order_number, $m)) {
                $nextNumber = ((int) $m[1]) + 1;
            }

            // Return formatted string: e.g., SA-AB26392
            return sprintf('%s%03d', $basePrefix, $nextNumber);
        }, 3);
    }

    // Define the relationship to the customer (NewLead)
    public function customer()
    {
        return $this->belongsTo(NewLeads::class, 'customer_id');
    }

    // Define the relationship to the product (ArticleGroup)
    public function product()
    {
        return $this->belongsTo(ArticleGroup::class, 'product_id');
    }

    // Define the relationship to the alternative (LeadAlternativeAdd)
    public function alternative()
    {
        return $this->belongsTo(LeadAlternativeAdd::class, 'alternative_id');
    }

    public function servicePhase()
    {
        return $this->belongsTo(PhaseSection::class, 'service_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function author()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function checkedBy()
    {
        return $this->belongsTo(Employee::class, 'checked_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(Employee::class, 'reviewer_id');
    }

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    public function folder()
    {
        return $this->belongsTo(OfferFolder::class, 'offer_folder_id');
    }

    /**
     * Get the related OfferFolder ID via the OfferDetail
     */
    public function getRelatedFolderIdAttribute()
    {
        // Try to find the offer detail which contains the offer_folder_id
        $detail = OfferDetail::where('offer_id', $this->offer?->id)->first();
        return $detail?->offer_folder_id;
    }

    public function deliveryNotes()
    {
        return $this->hasMany(DeliveryNote::class, 'deal_id');
    }

    public function latestDeliveryNote()
    {
        return $this->hasOne(DeliveryNote::class, 'deal_id')->latestOfMany();
    }

    public function measurements()
    {
        return $this->hasMany(\App\Models\DealMeasurement::class);
    }

    public function latestMeasurement()
    {
        return $this->hasOne(\App\Models\DealMeasurement::class)->latestOfMany();
    }
}