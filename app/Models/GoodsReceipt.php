<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoodsReceipt extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'customer_id',
        'object_id',
        'lead_product_list_id',
        'article_group_id',
        'department_id',
        'accepted_by_employee_id',
        'orderer_employee_id',
        'created_by_employee_id',
        'updated_by_employee_id',
        'issued_by_employee_id',
        'received_at',
        'description',
        'note',
        'status',
        'inspection_status',
        'issue_description',
        'destination',
        'commission_details',
        'qty',
        'unit',
        'purchase_price',
        'outbound_at',
        'outbound_recipient',
        'outbound_project',
        'outbound_customer_id',
        'outbound_object_id',
        'meta',
    ];

    protected $casts = [
        'received_at' => 'datetime',
        'outbound_at' => 'datetime',
        'meta'        => 'array',
        'qty'         => 'decimal:2',
        'purchase_price' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(NewLeads::class, 'customer_id');
    }

    public function object()
    {
        return $this->belongsTo(LeadAlternativeAdd::class, 'object_id');
    }

    public function leadProductList()
    {
        return $this->belongsTo(LeadProductList::class, 'lead_product_list_id');
    }

    public function articleGroup()
    {
        return $this->belongsTo(ArticleGroup::class, 'article_group_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function acceptedByEmployee()
    {
        return $this->belongsTo(Employee::class, 'accepted_by_employee_id');
    }

    public function ordererEmployee()
    {
        return $this->belongsTo(Employee::class, 'orderer_employee_id');
    }

    public function createdByEmployee()
    {
        return $this->belongsTo(Employee::class, 'created_by_employee_id');
    }

    public function updatedByEmployee()
    {
        return $this->belongsTo(Employee::class, 'updated_by_employee_id');
    }

    public function issuedByEmployee()
    {
        return $this->belongsTo(Employee::class, 'issued_by_employee_id');
    }

    public function outboundCustomer()
    {
        return $this->belongsTo(NewLeads::class, 'outbound_customer_id');
    }

    public function outboundObject()
    {
        return $this->belongsTo(LeadAlternativeAdd::class, 'outbound_object_id');
    }

    public function notifications()
    {
        return $this->hasMany(GoodsReceiptNotification::class, 'goods_receipt_id');
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (!filled($search)) {
            return $query;
        }

        $search = trim($search);

        return $query->where(function (Builder $q) use ($search) {
            $q->where('code', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('outbound_recipient', 'like', "%{$search}%")
              ->orWhereHas('customer', function (Builder $qq) use ($search) {
                  $qq->where('customer_no', 'like', "%{$search}%")
                     ->orWhere('name', 'like', "%{$search}%")
                     ->orWhere('lastname', 'like', "%{$search}%")
                     ->orWhere('firma', 'like', "%{$search}%");
              })
              ->orWhereHas('object', function (Builder $qq) use ($search) {
                  $qq->where('object_name', 'like', "%{$search}%")
                     ->orWhere('city', 'like', "%{$search}%")
                     ->orWhere('street', 'like', "%{$search}%");
              })
              ->orWhereHas('articleGroup', function (Builder $qq) use ($search) {
                  $qq->where('article_group', 'like', "%{$search}%");
              });
        });
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when(!empty($filters['status']), fn ($q) => $q->where('status', $filters['status']))
            ->when(!empty($filters['inspection_status']), fn ($q) => $q->where('inspection_status', $filters['inspection_status']))
            ->when(!empty($filters['department_id']), fn ($q) => $q->where('department_id', $filters['department_id']))
            ->when(!empty($filters['customer_id']), fn ($q) => $q->where('customer_id', $filters['customer_id']))
            ->when(!empty($filters['object_id']), fn ($q) => $q->where('object_id', $filters['object_id']))
            ->when(!empty($filters['article_group_id']), fn ($q) => $q->where('article_group_id', $filters['article_group_id']))
            ->when(!empty($filters['destination']), fn ($q) => $q->where('destination', $filters['destination']))
            ->when(!empty($filters['date_from']), fn ($q) => $q->whereDate('received_at', '>=', $filters['date_from']))
            ->when(!empty($filters['date_to']), fn ($q) => $q->whereDate('received_at', '<=', $filters['date_to']));
    }

    public function attachments()
    {
        return $this->hasMany(GoodsReceiptAttachment::class);
    }

    public function inboundAttachments()
    {
        return $this->hasMany(GoodsReceiptAttachment::class)->where('scope', 'inbound');
    }

    public function outboundAttachments()
    {
        return $this->hasMany(GoodsReceiptAttachment::class)->where('scope', 'outbound');
    }

    public static function nextCode(): string
    {
        $year = now()->format('Y');

        $last = static::withTrashed()
            ->where('code', 'like', "WE-{$year}-%")
            ->orderByDesc('id')
            ->value('code');

        $nextNumber = 1;

        if ($last && preg_match('/WE-\d{4}-(\d+)/', $last, $matches)) {
            $nextNumber = ((int) $matches[1]) + 1;
        }

        return 'WE-' . $year . '-' . str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);
    }

    public function getCanBeIssuedAttribute(): bool
    {
        return $this->status === 'completed';
    }
}