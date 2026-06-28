<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyReportWorkPlace extends Model
{
    protected $fillable = [
        'type', 'branch_id', 'address', 'lat', 'lon', 'status', 'place_name'
    ];
}
