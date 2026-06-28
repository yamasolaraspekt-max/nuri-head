<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewLeadResponsibility extends Model
{
    use HasFactory;

    // Define the table name if it does not follow Laravel's convention
    protected $table = 'new_lead_responsibilities';

    // Define the fillable properties for mass assignment
    protected $fillable = [
        'new_lead_id',
        'employee_id',
        'current_employee',
        'alternative_id',
        'product_id',
        'status',
        'reason'
    ];

    // Define the relationships

    /**
     * Get the new lead associated with this responsibility.
     */
   public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(ArticleGroup::class, 'product_id', 'id');
    }

    public function newLead()
    {
        return $this->belongsTo(NewLeads::class, 'new_lead_id', 'id');
    }

    /**
     * Get the current employee associated with this responsibility.
     */
    public function currentEmployee()
    {
        return $this->belongsTo(Employee::class, 'current_employee');
    }

    public function alternative_add()
    {
        return $this->belongsTo(Employee::class, 'alternative_id');
    }


   
}
