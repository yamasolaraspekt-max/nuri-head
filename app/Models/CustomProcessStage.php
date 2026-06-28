<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\AuditableLead;
class CustomProcessStage extends Model
{
  use AuditableLead;
  
  public  $fillable = [
        'custom_process_id',
        'employee_id', 
        'product_id', 
        'customer_id', 
        'alternative_id', 
        'service', 
        'status'
    ];
}
