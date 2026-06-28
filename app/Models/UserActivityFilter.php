<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserActivityFilter extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_ids',
        'employee_ids',
        'product_ids',
        'is_muted',
        'notification_types',
    ];

    protected $casts = [
        'customer_ids'       => 'array',
        'employee_ids'       => 'array',
        'product_ids'        => 'array',
        'notification_types' => 'array',
        'is_muted'           => 'boolean',
    ];

    protected $attributes = [
        'customer_ids'       => '[]',
        'employee_ids'       => '[]',
        'product_ids'        => '[]',
        'notification_types' => '[]',
        'is_muted'           => false,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}