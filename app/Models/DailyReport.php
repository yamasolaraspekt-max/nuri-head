<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DailyReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'start_date',
        'end_date',
        'address',
        'lat',
        'lon',
        'ip',
        'status',
    ];

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function times()
    {
        return $this->hasMany(DailyReportTime::class);
    }
   
}

