<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketReport extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'ticket_id',
        'employee_id',
        'customer_id',
        'alternative_id',
        'product_id',
        'title',
        'report',
        'language',
        'report_date',
        'likes',
    ];

    protected $casts = [
        'report_date' => 'datetime',
        'likes' => 'integer',
    ];

    public function ticket()
    {
        return $this->belongsTo(Problem::class, 'ticket_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
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

    public function comments()
    {
        return $this->hasMany(TicketReportComment::class, 'ticket_report_id');
    }
}