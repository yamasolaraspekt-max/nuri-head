<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use App\Models\MainAppointment;
use App\Models\PersonalTask;
use App\Models\Employee;

class DealMeasurement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'deal_id',
        'offer_id',
        'offer_folder_id',
        'offer_detail_id',
        'customer_id',
        'alternative_id',
        'product_id',
        'department_id',
        'measurement_no',
        'order_number',
        'offer_no',
        'status',
        'sent_at',
        'sent_by',
        'sections_snapshot',
        'material_summary',
        'note',
        'created_by',
        'updated_by',

        'materials_snapshot',
        'materials_approved_count',
        'materials_total_count',
        'materials_saved_at',
        'appointment_id',
        'personal_task_id',
        'responsible_employee_id',
        'scheduled_start_date',
        'scheduled_end_date',
        'scheduled_start_time',
        'scheduled_end_time',
        'assignment_description',
        'assignment_status',
        'assigned_at',
        'assigned_by',
    ];

    protected $casts = [
        'sections_snapshot' => 'array',
        'material_summary'  => 'array',
        'sent_at'           => 'datetime', 
        'materials_snapshot' => 'array',
        'materials_saved_at' => 'datetime',
        'scheduled_start_date' => 'date',
        'scheduled_end_date' => 'date',
        'assigned_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (DealMeasurement $measurement) {
            if (blank($measurement->measurement_no)) {
                $measurement->measurement_no = static::generateMeasurementNo();
            }
        });
    }

   
    public static function generateMeasurementNo(): string
    {
        return DB::transaction(function () {
            $year = date('y');
            $basePrefix = 'FA-' . $year;

            $last = static::query()
                ->where('measurement_no', 'like', $basePrefix . '%')
                ->lockForUpdate()
                ->orderByDesc('id')
                ->first();

            $nextNumber = 1;

            if ($last && preg_match('/^' . preg_quote($basePrefix, '/') . '(\d{3,})$/', $last->measurement_no, $m)) {
                $nextNumber = ((int) $m[1]) + 1;
            }

            return sprintf('%s%03d', $basePrefix, $nextNumber);
        }, 3);
    }

    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    public function folder()
    {
        return $this->belongsTo(OfferFolder::class, 'offer_folder_id');
    }

    public function detail()
    {
        return $this->belongsTo(OfferDetail::class, 'offer_detail_id');
    }

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
 
    public function creator()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }


    public function getProductLabelAttribute(): string
    {
        return (string) (
            $this->product?->article_group
            ?? $this->product?->name
            ?? $this->product?->title
            ?? $this->product_name
            ?? ''
        );
    }

    public function getMeasurementKindAttribute(): string
    {
        $text = mb_strtolower($this->product_label . ' ' . ($this->type ?? '') . ' ' . ($this->category ?? ''));

        $pvWords = [
            'pv',
            'photovoltaik',
            'photovoltaic',
            'solar',
            'solaranlage',
            'solar anlage',
            'photovoltaikanlage',
        ];

        foreach ($pvWords as $word) {
            if (str_contains($text, $word)) {
                return 'PV';
            }
        }

        $wpWords = [
            'wp',
            'wärmepumpe',
            'waermepumpe',
            'wärempumpe',
            'heatpump',
            'heat pump',
            'luft-wasser',
            'sole-wasser',
        ];

        foreach ($wpWords as $word) {
            if (str_contains($text, $word)) {
                return 'WP';
            }
        }

        return 'OTHER';
    }

    public function getIsPvAttribute(): bool
    {
        return $this->measurement_kind === 'PV';
    }

    public function getIsWpAttribute(): bool
    {
        return $this->measurement_kind === 'WP';
    }

    public function items()
    {
        return $this->hasMany(DealMeasurementItem::class, 'deal_measurement_id')
            ->orderBy('section_title')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function editDetail()
    {
        return $this->hasOne(DealMeasurementDetail::class, 'deal_measurement_id');
    }

    public function histories()
    {
        return $this->hasMany(\App\Models\DealMeasurementHistory::class, 'deal_measurement_id')
            ->latest();
    }

    public function appointment()
    {
        return $this->belongsTo(MainAppointment::class, 'appointment_id');
    }

    public function personalTask()
    {
        return $this->belongsTo(PersonalTask::class, 'personal_task_id');
    }

    public function responsibleEmployee()
    {
        return $this->belongsTo(Employee::class, 'responsible_employee_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(Employee::class, 'assigned_by');
    }
 
 

}