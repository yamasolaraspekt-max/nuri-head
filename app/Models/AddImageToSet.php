<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddImageToSet extends Model
{
    use HasFactory;
    protected $fillable = [
        'master_set_id', 'product_id', 'name', 'image',  'status'
    ];
}
