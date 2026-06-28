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
}
