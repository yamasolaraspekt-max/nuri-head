<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InquiryProductList extends Model
{
    use HasFactory;

    protected $fillable = [
        'inquiry_id',
        'department_id',
        'employee_id',
        'product_id',
        'service_id',
        'status',
        'appointment_date',
        'field_employee'
    ];

    protected $casts = [
        'appointment_date' => 'datetime',
    ];
    /**
     * Relationships
     */

    public function inquiry()
    {
        return $this->belongsTo(Inquiry::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function product()
    {
        return $this->belongsTo(ArticleGroup::class, 'product_id');
    }

    public function service()
    {
        return $this->belongsTo(PhaseSection::class, 'service_id');
    }
}
