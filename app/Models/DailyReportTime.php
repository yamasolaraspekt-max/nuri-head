<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class DailyReportTime extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'work_place_id',
        'customer_id',
        'report_date',
        'reportable_type',
        'reportable_id',
        'type',
        'description',
        'start_time',
        'end_time',
        'hours_spent',
        'status',
        'work_status',
        'address',
        'lat',
        'lon',
        'ip',

        'billing_type',
        'activity_category',
        'is_travel',
    ];

    protected $casts = [
        'report_date' => 'date',
        'hours_spent' => 'decimal:2',
        'lat' => 'decimal:6',
        'lon' => 'decimal:6',
        'is_travel' => 'boolean',
    ];

    public function reportable(): MorphTo
    {
        return $this->morphTo();
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function workplace()
    {
        return $this->belongsTo(DailyReportWorkPlace::class, 'work_place_id');
    }

    public function customers()
    {
        return $this->belongsToMany(
            NewLeads::class,
            'daily_report_time_customers',
            'report_time_id',
            'customer_id'
        )
            ->withPivot([
                'alternative_id',
                'lead_product_list_id',
                'product_id',
                'share_start_time',
                'share_end_time',
                'share_hours',
                'share_percent',
                'note',
            ])
            ->withTimestamps();
    }

    public function customerTimeRows()
    {
        return $this->hasMany(DailyReportTimeCustomer::class, 'report_time_id');
    }

    public function shareHoursFor($customerId): ?float
    {
        $rel = $this->customers->firstWhere('id', $customerId);

        if (!$rel) {
            return null;
        }

        if (!is_null($rel->pivot->share_hours)) {
            return (float) $rel->pivot->share_hours;
        }

        if (!is_null($rel->pivot->share_percent) && $this->hours_spent) {
            return round((float) $this->hours_spent * ((float) $rel->pivot->share_percent / 100), 2);
        }

        return null;
    }

    public function shareHoursForProduct($customerId, $leadProductListId = null, $alternativeId = null, $productId = null): ?float
    {
        $query = $this->customerTimeRows()
            ->where('customer_id', $customerId);

        if ($leadProductListId) {
            $query->where('lead_product_list_id', $leadProductListId);
        }

        if ($alternativeId) {
            $query->where('alternative_id', $alternativeId);
        }

        if ($productId) {
            $query->where('product_id', $productId);
        }

        return (float) $query->sum('share_hours') ?: null;
    }
}