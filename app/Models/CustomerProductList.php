<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\AuditableLead;
class CustomerProductList extends Model
{
    use HasFactory;
use AuditableLead;
    protected $table ="customer_product_lists";

    public $fillable = [
        'customer_id','product_id', 'status', 'created_at', 'updated_at'
    ];
}
