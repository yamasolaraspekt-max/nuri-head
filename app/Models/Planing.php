<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Planing extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_id', 'product_id', 'service', 'employee_id', 'plan_tools', 'status', 'status_msg', 'alternative_id'
    ];
}
