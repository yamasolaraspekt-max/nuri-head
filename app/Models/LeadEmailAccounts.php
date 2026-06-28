<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadEmailAccounts extends Model
{
    protected $fillable = [
        'label',
        'email',
        'password',
        'host',
        'port',
        'encryption',
        'status',
        'test',
    ];
}