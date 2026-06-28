<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MachineService extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'machine_id',
        'service_type',
        'service_date',
        'service_by',
        'price',
        'service_station',
        'technician',
        'location',
        'email',
        'phone',
        'service_report',
        'maintenance_interval',
        'fault_description',
        'repair_description',
        'fault_detected_at',
        'fault_detected_by',
        'fault_detected_location',
        'paid_by',
        'status',
    ];

    protected $casts = [
        'service_date' => 'date',
        'fault_detected_at' => 'datetime',
        'price' => 'decimal:2',
        'maintenance_interval' => 'integer',
    ];

    public const STATUS_OPEN = 'open';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_DONE = 'done';
    public const STATUS_CANCELLED = 'cancelled';

    public static function statuses(): array
    {
        return [
            self::STATUS_OPEN => 'Offen',
            self::STATUS_IN_PROGRESS => 'In Bearbeitung',
            self::STATUS_DONE => 'Erledigt',
            self::STATUS_CANCELLED => 'Abgebrochen',
        ];
    }

    public function machine()
    {
        return $this->belongsTo(Machine::class, 'machine_id');
    }

    public function serviceEmployee()
    {
        return $this->belongsTo(Employee::class, 'service_by');
    }

    public function faultDetectedByEmployee()
    {
        return $this->belongsTo(Employee::class, 'fault_detected_by');
    }

    public function getReportUrlAttribute(): ?string
    {
        return $this->service_report ? asset('documents/machine-services/' . $this->service_report) : null;
    }
}
