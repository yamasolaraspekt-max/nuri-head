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

    // IMAP-Passwort verschluesselt in der DB ablegen (Cast greift bei Eloquent create/save/update).
    protected $casts = [
        'password' => 'encrypted',
    ];
}