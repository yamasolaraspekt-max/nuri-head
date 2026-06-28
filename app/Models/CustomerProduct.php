<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\AuditableLead;
class CustomerProduct extends Model
{
    use HasFactory;
    use AuditableLead;

    protected $table = 'customer_products';

    public $fillable = ['customer_id', 'product_id'];
}
