<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\AuditableLead;
class CustomerHistory extends Model
{
    use HasFactory;
use AuditableLead;
    protected $fillable = [
        'customer_id',
        'alternative_id',
        'product_id',
        'phase_id',
        'activity_id',
        'section_id',
        'done_by',
        'marked_by',
        'is_done',
        'done_reason',
        'plan_time',
        'is_time',
        'done_date',
        'notes',
        'has_document',
        'done_history',
        'old_stage', 
    ];

    protected $casts = [
        'has_document' => 'array',
        'done_history' => 'array',
        'done_date'    => 'datetime:Y-m-d',
         'plan_time' => 'string',
        'is_time'   => 'string',
        'd_time'    => 'string',
        'done_reason' => 'array', 
    ];

    // 🔗 Relationships

    public function product()
    {
        return $this->belongsTo(ArticleGroup::class, 'product_id');
    }

    public function phase()
    {
        return $this->belongsTo(TaskPhase::class, 'phase_id');
    }

    public function activity()
    {
        return $this->belongsTo(PhaseActivities::class, 'activity_id');
    }

    public function section()
    {
        return $this->belongsTo(PhaseSection::class, 'section_id');
    }

    public function customer()
    {
        return $this->belongsTo(NewLeads::class, 'customer_id');
    }

    public function alternative()
    {
        return $this->belongsTo(LeadAlternativeAdd::class, 'alternative_id');
    }

    public function responsible()
    {
        return $this->belongsTo(Employee::class, 'done_by');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'done_by');
    }


    public function doneBy()
    {
        return $this->belongsTo(Employee::class, 'done_by');
    }

    public function markedBy()
    {
        return $this->belongsTo(Employee::class, 'marked_by');
    }


   public static function getNextIncompleteActivity($customerId, $alternativeId, $productId, $phaseId)
{
    return self::where([
        ['customer_id', $customerId],
        ['alternative_id', $alternativeId],
        ['product_id', $productId],
        ['phase_id', $phaseId],
        ['is_done', false],
    ])->first();
}


public function getDTimeComputedAttribute(): ?string {
    if ($this->d_time) return $this->d_time;
    $toMin = fn($t)=>$t && preg_match('/^(\d{1,2}):(\d{2})/', $t, $m) ? $m[1]*60+$m[2] : null;
    $fmt   = fn($m)=>$m===null?null:(($m<0?'-':'').sprintf('%02d:%02d:00', intdiv(abs($m),60), abs($m)%60));
    $p=$toMin($this->plan_time); $i=$toMin($this->is_time);
    return ($p!==null && $i!==null) ? $fmt($i-$p) : null;
}


}
