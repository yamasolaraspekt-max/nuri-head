<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalPersonal extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name','price_type', 'price', 'tax_id', 'product_id', 'admin_name', 'email', 'phone', 'status'
    ];
}
