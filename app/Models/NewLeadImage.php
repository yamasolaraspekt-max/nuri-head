<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\AuditableLead;
class NewLeadImage extends Model
{
    use HasFactory;
    use AuditableLead;

    protected  $fillable = [
        'name', 
        'image', 
        'lead_id',
        'category_id'
    ];
}
