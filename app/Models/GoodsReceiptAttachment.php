<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsReceiptAttachment extends Model
{
    protected $fillable = [
        'goods_receipt_id',
        'scope',
        'kind',
        'label',
        'original_name',
        'file_name',
        'file_path',
        'disk',
        'mime_type',
        'file_size',
        'uploaded_by_employee_id',
    ];

    protected $appends = [
        'file_url',
        'is_image',
    ];

    public function goodsReceipt()
    {
        return $this->belongsTo(GoodsReceipt::class);
    }

    public function uploadedByEmployee()
    {
        return $this->belongsTo(Employee::class, 'uploaded_by_employee_id');
    }

    public function getFileUrlAttribute(): ?string
    {
        if (!$this->file_path) {
            return null;
        }

        return \Storage::disk($this->disk ?: 'public')->url($this->file_path);
    }

    public function getIsImageAttribute(): bool
    {
        return str_starts_with((string) $this->mime_type, 'image/');
    }
}