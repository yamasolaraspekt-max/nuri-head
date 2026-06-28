<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\AuditableLead;

class CustomerStage extends Model
{
    use AuditableLead;

    protected $table = 'customer_stages';

    protected $fillable = [
        'customer_id',
        'alternative_id',
        'product_id',
        'section_id',
        'phase_id',
        'task_id',
        'stage_id',
        'version',
        'status',
    ];

    protected $casts = [
        'customer_id' => 'integer',
        'alternative_id' => 'integer',
        'product_id' => 'integer',
        'section_id' => 'integer',
        'phase_id' => 'integer',
        'task_id' => 'integer',
        'stage_id' => 'integer',
    ];

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

    public function section()
    {
        return $this->belongsTo(PhaseSection::class, 'section_id');
    }

    public function phase()
    {
        return $this->belongsTo(TaskPhase::class, 'phase_id');
    }

    public function task()
    {
        return $this->belongsTo(PhaseActivities::class, 'task_id');
    }

    public function stage()
    {
        return $this->belongsTo(Stage::class, 'stage_id');
    }
}