<?php
 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportedIdsItem extends Model
{
    protected $fillable = [
        'batch_id',
        'article_no',
        'ean',
        'short_text',
        'long_text',
        'qty',
        'unit',
        'offer_price',
        'net_price',
        'vat',
    ];
}
