<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\AuditableLead;
class CustomerReportComment extends Model
{
    use AuditableLead;
    protected $fillable = ['report_id', 'user_id', 'comment', 'parent_id'];

    public function user()
    {
        return $this->belongsTo(Employee::class, 'user_id');
    }

    public function report()
    {
        return $this->belongsTo(CustomerReport::class, 'report_id');
    }

    public function parent()
    {
        return $this->belongsTo(CustomerReportComment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(CustomerReportComment::class, 'parent_id')->latest();
    }
}
