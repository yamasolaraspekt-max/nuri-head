<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\AuditableLead;

class CustomerNote extends Model
{
    use HasFactory, SoftDeletes, AuditableLead;

    protected $fillable = [
        'customer_id',
        'alternative_id',
        'product_id',
        'lead_product_list_id',
        'created_by',
        'description',
        'color',
        'order_no',
        'parent_id',
        'due_date',
        'stage',
        'type',
        'history',
        'read_by',
        'last_read_at',

        // Kanban stage context
        'lead_stage_id',
        'lead_stage_key',
        'lead_stage_name',
        'lead_stage_color',
        'lead_stage_sub_stage_id',
        'lead_stage_sub_stage_name',
        'lead_stage_sub_stage_color',
    ];

    protected $casts = [
        'due_date' => 'date',
        'history' => 'array',
        'read_by' => 'array',
        'last_read_at' => 'datetime',
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

    public function leadProductList()
    {
        return $this->belongsTo(LeadProductList::class, 'lead_product_list_id');
    }

    public function leadStage()
    {
        return $this->belongsTo(LeadStage::class, 'lead_stage_id');
    }

    public function leadStageSubStage()
    {
        return $this->belongsTo(LeadStageSubStage::class, 'lead_stage_sub_stage_id');
    }

    public function replies()
    {
        return $this->hasMany(CustomerNote::class, 'parent_id')
            ->whereNull('deleted_at')
            ->with('creator');
    }

    public function repliesWithTrashed()
    {
        return $this->hasMany(CustomerNote::class, 'parent_id')
            ->withTrashed()
            ->with('creator');
    }

    public function parent()
    {
        return $this->belongsTo(CustomerNote::class, 'parent_id');
    }

    public function creator()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

    public function author()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function getStageContextAttribute(): array
    {
        return [
            'lead_stage_id' => $this->lead_stage_id,
            'lead_stage_key' => $this->lead_stage_key ?: $this->stage,
            'lead_stage_name' => $this->lead_stage_name ?: optional($this->leadStage)->name,
            'lead_stage_color' => $this->lead_stage_color ?: optional($this->leadStage)->color,
            'lead_stage_sub_stage_id' => $this->lead_stage_sub_stage_id,
            'lead_stage_sub_stage_name' => $this->lead_stage_sub_stage_name ?: optional($this->leadStageSubStage)->name,
            'lead_stage_sub_stage_color' => $this->lead_stage_sub_stage_color ?: optional($this->leadStageSubStage)->color,
        ];
    }

    public function appendHistory(string $action, ?int $userId = null, ?string $userName = null, array $meta = []): void
    {
        $history = $this->history ?? [];

        $history[] = [
            'action' => $action,
            'at' => now()->toDateTimeString(),
            'user_id' => $userId,
            'user_name' => $userName,
            'meta' => $meta,
        ];

        $this->history = $history;
    }

    public function markAsReadBy(?int $userId = null, ?string $userName = null): bool
    {
        if (!$userId) {
            return false;
        }

        $readBy = collect($this->read_by ?? []);

        $alreadyRead = $readBy->contains(fn($item) => (int) ($item['user_id'] ?? 0) === (int) $userId);

        if ($alreadyRead) {
            return false;
        }

        $readBy->push([
            'user_id' => $userId,
            'user_name' => $userName,
            'read_at' => now()->toDateTimeString(),
        ]);

        $this->read_by = $readBy->values()->all();
        $this->last_read_at = now();

        return true;
    }
}
