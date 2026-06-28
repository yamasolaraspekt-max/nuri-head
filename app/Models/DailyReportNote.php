<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DailyReportNote extends Model
{
    protected $fillable = [
        'report_id',
        'employee_id',
        'daily_report_time_id',
        'report_date',
        'message',
    ];

    protected $casts = [
        'report_date' => 'date:Y-m-d',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(DailyReport::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function time(): BelongsTo
    {
        return $this->belongsTo(DailyReportTime::class, 'daily_report_time_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(DailyReportAttachment::class, 'note_id');
    }
}
