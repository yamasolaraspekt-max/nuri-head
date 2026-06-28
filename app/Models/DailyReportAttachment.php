<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DailyReportAttachment extends Model
{
    protected $fillable = [
        'note_id',
        'employee_id',
        'disk',
        'path',
        'original_name',
        'mime',
        'size',
    ];

    protected $appends = [
        'url',
        'ext',
        'is_image',
        'size_label',
    ];

    public function note(): BelongsTo
    {
        return $this->belongsTo(DailyReportNote::class, 'note_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function getUrlAttribute(): string
    {
        if (empty($this->path)) {
            return '';
        }

        $disk = $this->disk ?: 'public';

        $url = Storage::disk($disk)->url($this->path);

        // Normalize: make it absolute/root-relative to avoid route-prefix concatenation.
        if (!Str::startsWith($url, ['http://', 'https://', '/'])) {
            $url = '/' . ltrim($url, '/');
        }

        return $url;
    }

    public function getExtAttribute(): string
    {
        $name = $this->original_name ?: $this->path ?: '';

        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

        if ($ext !== '') {
            return $ext;
        }

        return strtolower(explode('/', (string) $this->mime)[1] ?? '');
    }

    public function getIsImageAttribute(): bool
    {
        if ($this->mime && Str::startsWith($this->mime, 'image/')) {
            return true;
        }

        return in_array($this->ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true);
    }

    public function getSizeLabelAttribute(): string
    {
        $bytes = (int) ($this->size ?? 0);

        if ($bytes >= 1024 * 1024) {
            return number_format($bytes / (1024 * 1024), 2, ',', '.') . ' MB';
        }

        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 1, ',', '.') . ' KB';
        }

        return $bytes . ' B';
    }
}