<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketReportComment extends Model
{
    protected $fillable = [
        'ticket_id',
        'ticket_report_id',
        'liked_by',
        'comment_by',
        'customer_id',
        'alternative_id',
        'product_id',
        'comment',
    ];

    public function ticket()
    {
        return $this->belongsTo(Problem::class, 'ticket_id');
    }

    public function report()
    {
        return $this->belongsTo(TicketReport::class, 'ticket_report_id');
    }

    public function likedBy()
    {
        return $this->belongsTo(Employee::class, 'liked_by');
    }

    public function commentedBy()
    {
        return $this->belongsTo(Employee::class, 'comment_by');
    }

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
}