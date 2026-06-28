<?php

// app/Models/InquiryReport.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InquiryReport extends Model
{
    protected $fillable = ['inquiry_id','report_by','report','meta','report_date','due_date'];
    protected $casts = ['meta' => 'array', 'report_date' => 'datetime', 'due_date' => 'datetime'];

    public function inquiry(){ return $this->belongsTo(Inquiry::class); }
    public function reporter(){ return $this->belongsTo(Employee::class, 'report_by'); }
}
