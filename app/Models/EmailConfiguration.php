<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailConfiguration extends Model
{
    use HasFactory;

    protected $fillable=[
        'name', 'host', 'port', 'encryption', 'validate_cert', 'username', 'password', 'protocol', 'status'
    ];

    // IMAP-Passwort verschluesselt in der DB ablegen (Cast greift bei Eloquent create/save/update).
    protected $casts = [
        'password' => 'encrypted',
    ];
}
