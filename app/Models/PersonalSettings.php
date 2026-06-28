<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonalSettings extends Model
{
    protected $fillable = ['employee_id', 'calendar_settings'];

    protected $casts = [
        'calendar_settings' => 'array'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}

