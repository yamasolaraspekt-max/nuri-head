<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Machine extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'model',
        'year',
        'color',
        'engine_type',
        'mileage',
        'serial_no',
        'purchase_type',
        'purchase_price',
        'purchase_date',
        'leasing_from',
        'leasing_start_date',
        'leasing_end_date',
        'leasing_price',
        'description',
        'last_service_date',
        'technical_inspection',
        'technical_inspection_date',
        'branch_id',
        'department_id',
        'article_group',
        'owner_name',
        'owner_contact',
        'status',
        'image',
    ];

    protected $casts = [
        'year' => 'integer',
        'mileage' => 'integer',
        'purchase_price' => 'decimal:2',
        'leasing_price' => 'decimal:2',
        'purchase_date' => 'date',
        'leasing_start_date' => 'date',
        'leasing_end_date' => 'date',
        'last_service_date' => 'date',
        'technical_inspection' => 'boolean',
        'technical_inspection_date' => 'date',
    ];

    public const STATUS_ACTIVE = 'active';
    public const STATUS_IN_SERVICE = 'in_service';
    public const STATUS_REPAIR = 'repair';
    public const STATUS_INSPECTION_DUE = 'inspection_due';
    public const STATUS_INACTIVE = 'inactive';

    public static function statuses(): array
    {
        return [
            self::STATUS_ACTIVE => 'Aktiv',
            self::STATUS_IN_SERVICE => 'Im Service',
            self::STATUS_REPAIR => 'Reparatur',
            self::STATUS_INSPECTION_DUE => 'TÜV fällig',
            self::STATUS_INACTIVE => 'Inaktiv',
        ];
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /** Existing database column is article_group, so this keeps current data compatible. */
    public function articleGroup()
    {
        return $this->belongsTo(ArticleGroup::class, 'article_group');
    }

    public function services()
    {
        return $this->hasMany(MachineService::class, 'machine_id');
    }

    public function latestService()
    {
        return $this->hasOne(MachineService::class, 'machine_id')->latestOfMany('service_date');
    }

    public function installments()
    {
        return $this->hasMany(AssetInstallment::class, 'asset_id')->where('type', 'machine');
    }

    public function activeInstallment()
    {
        return $this->hasOne(AssetInstallment::class, 'asset_id')->where('type', 'machine')->latestOfMany();
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('images/asset/car/' . $this->image) : null;
    }

    public function getDisplayNameAttribute(): string
    {
        return trim(($this->name ?? '') . ' ' . ($this->model ?? '')) ?: '#' . $this->id;
    }
}
