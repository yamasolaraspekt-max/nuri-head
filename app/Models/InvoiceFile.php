<?php

namespace App\Models;

use App\Exceptions\InvoiceDeletionBlockedException;
use App\Services\Invoice\InvoiceDeletionGuard;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceFile extends Model
{
    use SoftDeletes;

    protected static function booted(): void
    {
        // S1-02: Dateien finaler Rechnungen duerfen nicht geloescht werden.
        static::deleting(function (self $file): void {
            $result = app(InvoiceDeletionGuard::class)->canDeleteFile($file);

            if (!$result->allowed) {
                throw new InvoiceDeletionBlockedException($result->message, $result->code);
            }
        });
    }

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
