<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class EmployeeSick extends Model
{
    use HasFactory;

    protected $fillable = [
        'emp_id',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'total_days',
        'total_hours',
        'year',
        'status',
        'document',
        'status_msg',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_days' => 'float',
        'total_hours' => 'float',
    ];

    protected $appends = [
        'documents',
        'document_urls',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_id');
    }

    public function creator()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

    public function calculateDuration()
    {
        if (!$this->start_date) {
            return 0;
        }

        $startDate = Carbon::parse($this->start_date);
        $endDate = $this->end_date ? Carbon::parse($this->end_date) : $startDate->copy();

        return $startDate->diffInDays($endDate) + 1;
    }

    public function getDocumentsAttribute(): array
    {
        if (!$this->document) {
            return [];
        }

        if (is_array($this->document)) {
            return array_values(array_filter($this->document));
        }

        $value = trim((string) $this->document);

        if ($value === '') {
            return [];
        }

        $decoded = json_decode($value, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return array_values(array_filter($decoded));
        }

        // Support old records where document was only one path/string
        return [$value];
    }

    public function getDocumentUrlsAttribute(): array
    {
        return collect($this->documents)
            ->map(fn($path) => asset($path))
            ->values()
            ->toArray();
    }

    public function getDocumentUrlAttribute()
    {
        $documents = $this->documents;

        return count($documents)
            ? asset($documents[0])
            : null;
    }
}