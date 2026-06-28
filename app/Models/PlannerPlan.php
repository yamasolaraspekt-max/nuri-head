<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlannerPlan extends Model
{
    use SoftDeletes;

    protected $table = 'planner_plans';

    protected $fillable = [
        'account_id',
        'customer_id',     // -> new_leads.id
        'project_id',      // -> lead_product_lists.id (nullable)
        'stage',
        'title',
        'status',
        'created_by',
        'published_at',
        'meta',
    ];

    protected $casts = [
        'account_id'    => 'integer',
        'customer_id'   => 'integer',
        'project_id'    => 'integer',
        'created_by'    => 'integer',
        'published_at'  => 'datetime',
        'meta'          => 'array',
    ];

    // Optional default ordering for queries
    protected $attributes = [
        'status' => 'draft',
    ];

    /* -------------------------------------------------------------------------
     | Relationships
     * ---------------------------------------------------------------------- */

    public function items(): HasMany
    {
        return $this->hasMany(PlannerItem::class, 'plan_id')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    // Customer = new_leads
    public function customer(): BelongsTo
    {
        return $this->belongsTo(NewLeads::class, 'customer_id');
    }

    // Project/Product/Site row = lead_product_lists
    public function project(): BelongsTo
    {
        return $this->belongsTo(LeadProductList::class, 'project_id');
    }

    // If you have accounts table/model (multi-tenant)
    public function account(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'id');
    }

    // If created_by references users
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /* -------------------------------------------------------------------------
     | Scopes (useful for your bootstrap queries)
     * ---------------------------------------------------------------------- */

    public function scopeForCustomer($q, int $customerId)
    {
        return $q->where('customer_id', $customerId);
    }

    public function scopeForProject($q, ?int $projectId)
    {
        return $q->when($projectId, fn ($x) => $x->where('project_id', $projectId),
                              fn ($x) => $x->whereNull('project_id'));
    }

    public function scopeForStage($q, string $stage)
    {
        return $q->where('stage', $stage);
    }

    public function scopePublished($q)
    {
        return $q->where('status', 'published');
    }

    public function getDisplayTitleAttribute(): string
    {
        $t = trim((string)($this->title ?? ''));
        if ($t !== '') return $t;

        $stage = trim((string)($this->stage ?? ''));
        $stage = $stage !== '' ? strtoupper($stage) : 'PLAN';
        return "{$stage} · #{$this->id}";
    }
}
