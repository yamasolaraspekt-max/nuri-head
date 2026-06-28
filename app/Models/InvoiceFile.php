<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceFile extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'invoice_id',
        'original_name',
        'stored_name',
        'stored_path',
        'mime',
        'size',
        'uploaded_by',
    ];

    protected $casts = [
        'size' => 'integer',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    // employees table
    public function uploader()
    {
        return $this->belongsTo(Employee::class, 'uploaded_by');
    }
}
