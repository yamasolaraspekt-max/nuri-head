<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerMeasure extends Model
{
    use HasFactory;
        protected $fillable = ['customer_id', 'measure_label', 'width', 'height', 'area'];

}
