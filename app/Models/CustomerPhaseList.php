<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\AuditableLead;
class CustomerPhaseList extends Model
{
    use HasFactory;
use AuditableLead;
    public $fillable = [
        'customer',
        'phase_id',
        'alternative', 
        'product',
        'service',
        'status',
        'color', 
        'active_by', 'jump_steps', 'jump_step_by', 
        'created_at',
        'updated_at'

    ];
     public function taskPhase()
    {
        return $this->belongsTo(TaskPhase::class, 'phase_id');
    }
}
