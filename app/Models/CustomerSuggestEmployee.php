<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\AuditableLead;
class CustomerSuggestEmployee extends Model
{
    
    use HasFactory;
    use AuditableLead;

        protected $table = 'customer_suggest_employees';

        protected $fillable = [
            'customer_id',
            'alternative_id',
            'product_id',
            'phase_id',
            'employee_id',
            'department_id',
            'role',
        ];

        // 🔁 Relationships

        public function customer()
        {
            return $this->belongsTo(\App\Models\NewLeads::class, 'customer_id');
        }

        public function alternative()
        {
            return $this->belongsTo(\App\Models\LeadAlternativeAdd::class, 'alternative_id');
        }

        public function product()
        {
            return $this->belongsTo(\App\Models\ArticleGroup::class, 'product_id');
        }

        public function phase()
        {
            return $this->belongsTo(\App\Models\TaskPhase::class, 'phase_id');
        }

        public function employee()
        {
            return $this->belongsTo(\App\Models\Employee::class, 'employee_id');
        }

        public function department()
        {
            return $this->belongsTo(\App\Models\Department::class, 'department_id');
        }
    public static function getSuggestedEmployees($customerId, $alternativeId, $productId, $phaseId)
    {
        return self::where('customer_id', $customerId)
            ->where('alternative_id', $alternativeId)
            ->where('product_id', $productId)
            ->where('phase_id', $phaseId)
            ->with('employee') // if relation exists
            ->get();
    }


}
