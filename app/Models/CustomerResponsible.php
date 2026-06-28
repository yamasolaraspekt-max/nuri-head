<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerResponsible extends Model
{
    use HasFactory;
      // Define the fillable properties for mass assignment
    protected $fillable = [
        'customer_id',
        'employee_id',
        'current_employee',
        'product_id',
        'status',
    ];

    // Define the relationships

    /**
     * Get the new lead associated with this responsibility.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the employee associated with this responsibility.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * Get the current employee associated with this responsibility.
     */
    public function currentEmployee()
    {
        return $this->belongsTo(Employee::class, 'current_employee');
    }
}
