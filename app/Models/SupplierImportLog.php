<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierImportLog extends Model
{
    protected $fillable = [
        'supplier_connection_id',
        'status',
        'source_type',
        'total_items',
        'success_items',
        'failed_items',
        'message',
        'payload',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function supplierConnection()
    {
        return $this->belongsTo(SupplierConnection::class);
    }
}