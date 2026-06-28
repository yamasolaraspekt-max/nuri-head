<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierConnection extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'distributor_id',
        'name',
        'supplier_key',
        'connector_type',
        'auth_type',
        'endpoint_url',
        'test_url',
        'return_url',
        'username',
        'password',
        'customer_number',
        'token',
        'extra_auth_data',
        'request_config',
        'import_config',
        'last_test_status',
        'last_test_message',
        'last_tested_at',
        'is_active',
    ];

    protected $casts = [
        'username' => 'encrypted',
        'password' => 'encrypted',
        'customer_number' => 'encrypted',
        'token' => 'encrypted',

        'extra_auth_data' => 'encrypted:array',

        'request_config' => 'array',
        'import_config' => 'array',

        'last_tested_at' => 'datetime',
        'is_active' => 'boolean',
    ];
    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }

    public function mappings()
    {
        return $this->hasMany(SupplierConnectionMapping::class);
    }

    public function activeMappings()
    {
        return $this->hasMany(SupplierConnectionMapping::class)
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    public function logs()
    {
        return $this->hasMany(SupplierImportLog::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->connector_type) {
            'ids' => 'IDS',
            'oci' => 'OCI',
            'api' => 'API',
            'csv' => 'CSV',
            'xml' => 'XML',
            'bmecat' => 'BMEcat',
            'datanorm' => 'DATANORM',
            default => strtoupper((string) $this->connector_type),
        };
    }
}