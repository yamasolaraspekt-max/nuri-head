<?php
// app/Models/PublicHoliday.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicHoliday extends Model
{
    protected $table = 'public_holidays';

    protected $fillable = [
        'name','comment','start_date','end_date','city','state','country'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];
}
