<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyReportTimeCustomer extends Model
{
    protected $table = 'daily_report_time_customers';

    protected $fillable = [
        'report_time_id',
        'customer_id',
        'alternative_id',
        'lead_product_list_id',
        'product_id',
        'share_start_time',
        'share_end_time',
        'share_hours',
        'share_percent',
        'note',
    ];

    protected $casts = [
        'share_hours' => 'decimal:2',
        'share_percent' => 'decimal:2',
    ];

    public function reportTime()
    {
        return $this->belongsTo(DailyReportTime::class, 'report_time_id');
    }

    public function customer()
    {
        return $this->belongsTo(NewLeads::class, 'customer_id');
    }

    public function object()
    {
        return $this->belongsTo(LeadAlternativeAdd::class, 'alternative_id');
    }

    public function leadProduct()
    {
        return $this->belongsTo(LeadProductList::class, 'lead_product_list_id');
    }

    public function product()
    {
        return $this->belongsTo(ArticleGroup::class, 'product_id');
    }
}
