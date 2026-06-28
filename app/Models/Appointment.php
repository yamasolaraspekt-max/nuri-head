<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'product_id',
        'phase_id',
        'activity_id',
        'postcode',
        'title',
        'description',
        'priority',
        'color',
        'start_date',
        'end_date',
        'report_date',
        'report_date_type',
        'start_time',
        'end_time',
        'update_start_date',
        'update_end_date',
        'update_start_time',
        'update_end_time',
        'total_hour',
        'updated_by',
        'update_reason',
        'created_by',
        'deleted_by',
        'delete_reason',
        'postpond_request',
        'postpond_reason'
    ];

 

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

   // Relationship to phases
    public function phases()
    {
        return $this->belongsToMany(Phase::class, 'activity_employees', 'activity_id', 'phase_id')
                    ->withPivot('employee_id')
                    ->withTimestamps();
    }

    // Relationship to activities
    public function activities()
    {
        return $this->belongsToMany(Activity::class, 'activity_employees', 'employee_id', 'activity_id')
                    ->withPivot('phase_id')
                    ->withTimestamps();
    }

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'activity_employees', 'phase_id', 'employee_id')
                    ->withPivot('activity_id')
                    ->withTimestamps();
    }

}
