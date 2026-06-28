<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerCardNote extends Model
{
    use HasFactory;

    protected $table = 'customer_card_notes';

    protected $fillable = [
        'customer_id',
        'alternative_id',
        'product_id',
        'title',
        'description',
    ];

    // 🔁 Relationships

    public function customer()
    {
        return $this->belongsTo(NewLeads::class, 'customer_id');
    }

    public function alternative()
    {
        return $this->belongsTo(LeadAlternativeAdd::class, 'alternative_id');
    }

    public function product()
    {
        return $this->belongsTo(ArticleGroup::class, 'product_id');
    }
}
