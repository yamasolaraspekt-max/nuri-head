<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryNote extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'delivery_note',
        'delivered_from',
        'destination_type',
        'branch_id',
        'customer_id',
        'alternative_id',
        'lead_product_list_id',
        'deal_id',
        'handover_by',
        'order_by',
        'order_no',
        'comission',
        'order_date',
        'handover_date',
        'description',
        'status',
        'progress',
        'pdf',
        'image',
        'linked',
        'linked_delivery_note_id',
        'level',
    ];

    protected $casts = [
        'order_date' => 'date',
        'handover_date' => 'date',
        'progress' => 'integer',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function handoverEmployee()
    {
        return $this->belongsTo(Employee::class, 'handover_by');
    }

    public function customer()
    {
        return $this->belongsTo(NewLeads::class, 'customer_id');
    }

    public function alternative()
    {
        return $this->belongsTo(LeadAlternativeAdd::class, 'alternative_id');
    }

    public function leadProductList()
    {
        return $this->belongsTo(LeadProductList::class, 'lead_product_list_id');
    }

    public function deal()
    {
        return $this->belongsTo(Deal::class, 'deal_id');
    }

    public function linkedDeliveryNote()
    {
        return $this->belongsTo(self::class, 'linked_delivery_note_id');
    }

    public function linkedNotes()
    {
        return $this->hasMany(self::class, 'linked_delivery_note_id');
    }

    public function images()
    {
        return $this->hasMany(DeliveryNoteImage::class, 'delivery_note_id');
    }
}