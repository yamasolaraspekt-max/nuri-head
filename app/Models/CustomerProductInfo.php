<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\AuditableLead;
class CustomerProductInfo extends Model
{
    use AuditableLead;
    protected $fillable = [
        'customer_id',
        'alternative_id',
        'department_id',
        'product_id',
        'employee_id',
        'products',
        'product_name',
        'manufacturer',
        'serial_number',
        'installation_date',
        'installation_location',
        'purchased_from_us',
        'purchase_date',
        'invoice_reference',
        'warranty_until',
        'guarantee_until',
        'image_available',
        'installed_by',
        'notes',
        'product_count',
        'serial_numbers' 
    ];


    protected $casts = [
        'serial_numbers' => 'array',
        'purchased_from_us' => 'boolean',
        'image_available' => 'boolean',
    ];

    public function media()
{
    return $this->hasMany(\App\Models\CustomerProductInfoMedia::class, 'customer_product_info_id');
}

 
    public function customer()
    {
        return $this->belongsTo(NewLeads::class, 'customer_id');
    }

    // ➕ Alternative Quote
    public function alternative()
    {
        return $this->belongsTo(LeadAlternativeAdd::class, 'alternative_id');
    }

 
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

   
    public function product()
    {
        return $this->belongsTo(ArticleGroup::class, 'product_id');
    }

    // 👷 Employee (stored also in new_leads table)
    public function employee()
    {
        return $this->belongsTo(NewLeads::class, 'employee_id');
    }
}
