<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerReview extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'alternative_id',
        'product_id',
        'employee_id',
        'stars',
        'behavior',
        'caution_note',
        'internet_feedback',
        'internal_note',
        'source',
        'is_critical',
    ];

    protected $casts = [
        'stars' => 'integer',
        'is_critical' => 'boolean',
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

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function getEmployeeNameAttribute(): string
    {
        if (!$this->employee) {
            return 'Unbekannt';
        }

        return trim(($this->employee->name ?? '') . ' ' . ($this->employee->lastname ?? '')) ?: '#' . $this->employee->id;
    }

    public function getStarsLabelAttribute(): string
    {
        return str_repeat('★', max(0, min(5, (int) $this->stars)));
    }

    public function getBehaviorLabelAttribute(): string
    {
        return match ($this->behavior) {
            'friendly' => 'Freundlich',
            'normal' => 'Normal',
            'difficult' => 'Schwierig',
            'aggressive' => 'Aggressiv',
            'unreliable' => 'Unzuverlässig',
            'price_sensitive' => 'Preissensibel',
            'very_good' => 'Sehr gut',
            default => $this->behavior ?: 'Nicht angegeben',
        };
    }
}