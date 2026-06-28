<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectTimeRequest extends Model
{
    protected $fillable = [
        'customer_id',
        'alternative_id',
        'product_id',
        'section_id',
        'requested_by',
        'approved_by',
        'extra_minutes',
        'status',
        'reason',
        'answer',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(NewLeads::class, 'customer_id');
    }

    public function alternative()
    {
        return $this->belongsTo(LeadAlternativeAdd::class, 'alternative_id');
    }

    public function product()
    {
        return $this->belongsTo(ArticleGroup::class, 'product_id');
    }

    public function requester()
    {
        return $this->belongsTo(Employee::class, 'requested_by');
    }

    public function approver()
    {
        return $this->belongsTo(Employee::class, 'approved_by');
    }
}
