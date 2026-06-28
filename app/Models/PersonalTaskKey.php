<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PersonalTaskKey extends Model
{
    //

        use SoftDeletes;

        protected $casts = [
            'done_date'   => 'date',
            'employee_id' => 'array', // JSON with employee IDs
            'done_date'   => 'date',
            'duration'    => 'decimal:2',
            'total_time'  => 'decimal:2',
        ];

        protected $fillable = [
            'personal_task_id',
            'same_id',
            'task',
            'duration',
            'link',
            'key_description',
            'status',
            'is_completed',
            'done_by',
            'done_date',
            'done_status',
            'work_progress',
            'submit_time',
            'total_time',
            'reason',
            'employee_id',
        ];

      public function task()
    {
        return $this->belongsTo(PersonalTask::class, 'personal_task_id');
    }

  

    /** If you want the frontend to automatically see employees, append it */
    protected $appends = ['employees']; // optional; remove if you don’t want it in every response

    // 🔁 Rename relation to avoid clashing with 'task' string column
    public function parentTask()
    {
        return $this->belongsTo(PersonalTask::class, 'personal_task_id');
    }

    public function doneBy()
    {
        return $this->belongsTo(Employee::class, 'done_by');
    }

    /** Computed helper: return Employee models from the JSON ids */
    public function getEmployeesAttribute()
    {
        $ids = collect($this->employee_id ?: [])->filter()->unique()->values()->all();
        if (empty($ids)) return collect();
        return \App\Models\Employee::whereIn('id', $ids)->get(['id','name','lastname','image']);
    }

    /** Handy scope for triplet filtering */
    public function scopeForLead($q, $customerId, $alternativeId, $productId = null)
    {
        $q->whereHas('parentTask', function ($qq) use ($customerId, $alternativeId, $productId) {
            $qq->where('customer_id', $customerId)
               ->where('alternative_id', $alternativeId);
            if ($productId) $qq->where('product_id', $productId);
        });
    }

}
