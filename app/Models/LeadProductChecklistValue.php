<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadProductChecklistValue extends Model
{
    protected $fillable = [
        'lead_product_list_id',
        'product_formula_id',
        'customer_id',
        'alternative_id',
        'product_id',
        'section_name',
        'filled_values',
        'formula_snapshot',
        'formula_version',
    ];

    protected $casts = [
        'filled_values' => 'array',
        'formula_snapshot' => 'array',
    ];

    // 🔗 One filled checklist belongs to a customer
    public function customer()
    {
        return $this->belongsTo(NewLeads::class, 'customer_id');
    }

    // 🔗 One filled checklist belongs to an alternative
    public function alternative()
    {
        return $this->belongsTo(LeadAlternativeAdd::class, 'alternative_id');
    }

    // 🔗 One filled checklist belongs to a product
    public function product()
    {
        return $this->belongsTo(ArticleGroup::class, 'product_id');
    }

    // 🔗 One filled checklist belongs to a product formula
    public function formula()
    {
        return $this->belongsTo(ProductFormula::class, 'product_formula_id');
    }

    // 🔗 One filled checklist belongs to a lead product list (customer-product link)
    public function leadProductList()
    {
        return $this->belongsTo(LeadProductList::class, 'lead_product_list_id');
    }
}
