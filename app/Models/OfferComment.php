<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferComment extends Model
{
    protected $fillable = [
        'customer_id',
        'alternative_id',
        'product_id',
        'employee_id',
        'parent_id',
        'comment'
    ];

    // Relationships

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

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
    

    public function parent()
    {
        return $this->belongsTo(OfferComment::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(OfferComment::class, 'parent_id');
    }
}
