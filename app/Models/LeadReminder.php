<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeadReminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_product_list_id',
        'customer_id',
        'alternative_id',
        'product_id',
        'department_id',
        'responsible_employee_id',
        'title',
        'description',
        'reminder_date',
        'reminder_time',
        'priority',
        'status',
        'created_by',
        'notified_at',
        'completed_at',
    ];

    protected $casts = [
        'reminder_date' => 'date',
        'notified_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function leadProductList()
    {
        return $this->belongsTo(LeadProductList::class);
    }

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

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function responsibleEmployee()
    {
        return $this->belongsTo(Employee::class, 'responsible_employee_id');
    }
}