<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OverdueReportRead extends Model
{
    protected $fillable = [
        'report_id',
        'employee_id',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];
}