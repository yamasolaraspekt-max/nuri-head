<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\AuditableLead;
class CustomerPhaseStage extends Model
{
    use HasFactory;
use AuditableLead;
    public $fillable = [
        'customer_id', 'phase_id', 'status'
    ];
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Define the relationship with the TaskPhase model
    public function phase()
    {
        return $this->belongsTo(TaskPhase::class);
    }
}
