<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\AuditableLead;
class CustomerReport extends Model
{
    use SoftDeletes;
use AuditableLead;
    protected $table = 'customer_reports';

    protected $fillable = [
        'customer_id',
        'alternative_id',
        'product_id',
        'report_by',
        'stage',
        'report',
        'report_details',
    ];

    protected $casts = [
        'report_details' => 'array',
    ];

    // 🔁 Relationships

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

    public function reporter()
    {
        return $this->belongsTo(Employee::class, 'report_by');
    }

    public function comments()
    {
        return $this->hasMany(CustomerReportComment::class, 'report_id')->whereNull('parent_id')->latest();
    }

}
