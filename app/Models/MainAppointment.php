<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\AppointmentReport;
use App\Models\AppointmentComment;
use App\Traits\AuditableLead;

class MainAppointment extends Model
{
    use HasFactory, SoftDeletes;
    use AuditableLead;

    protected $fillable = [
        'created_by',
        'name',
        'note',
        'color',
        'start_date',
        'end_date',
        'appointment_type',
        'link',
        'execution_type',
        'products',
        'is_report',
        'report',
        'report_date',
        'report_by',
        'start_time',
        'end_time',
        'repeat',
        'reminder_date',
        'reminder_time',
        'total_time',
        'source',
        'contact_mode',
        'report_responsible',
        'next_step',
        'other_id',
        'task_id',
        'deal_measurement_id',
        'change_date',
        'change_reason',
        'street',
        'latitude',
        'longitude',
        'full_address',
        'email',
        'contact_id',
        'contact_type',
        'postcode',
        'city',
        'phone',
        'branch_id',
        'customer_id',
        'changed_by',
        'status',
        'priority',
        'public',
        'is_notified',
        'type',
        'branch_address_id',
        'date_type',
        'from_day',
        'to_day',
        'from_month',
        'to_month',
        'is_contact',
        'pre_type',
        'problem_id',
        'problem_task_id',
        'planner_item_id',

        // CRM stage context for appointment popup + linked PersonalTask
        'lead_product_list_id',
        'lead_stage_id',
        'lead_stage_sub_stage_id',
    ];

    protected $casts = [
        'product_inquiry' => 'array',
        'report_responsible' => 'array',
        'products' => 'array',
        'report_date' => 'date',
        'is_report' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'deal_measurement_id' => 'integer',
        'lead_product_list_id' => 'integer',
        'lead_stage_id' => 'integer',
        'lead_stage_sub_stage_id' => 'integer',
    ];

    public function createdBy()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

    public function changedBy()
    {
        return $this->belongsTo(Employee::class, 'changed_by');
    }

    public function customer()
    {
        return $this->belongsTo(NewLeads::class, 'customer_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function branchAddress()
    {
        return $this->belongsTo(BranchAddress::class, 'branch_address_id');
    }

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'main_appointment_employees', 'appointment_id', 'employee_id')
            ->withPivot('status', 'reason')
            ->withTimestamps();
    }

    public function appointmentEmployees()
    {
        return $this->hasMany(MainAppointmentEmployee::class, 'appointment_id');
    }

    public function problem()
    {
        return $this->belongsTo(Problem::class, 'problem_id');
    }

    public function problemTask()
    {
        return $this->belongsTo(TicketTask::class, 'problem_task_id');
    }

    public function task()
    {
        return $this->belongsTo(PersonalTask::class, 'task_id');
    }

    public function reporter()
    {
        return $this->belongsTo(Employee::class, 'report_by');
    }

    public function reportAuthor()
    {
        return $this->belongsTo(Employee::class, 'report_by');
    }

    public function reports()
    {
        return $this->hasMany(AppointmentReport::class, 'appointment_id')->latest('report_date')->latest('created_at');
    }

    public function comments()
    {
        return $this->hasMany(AppointmentComment::class, 'appointment_id')->latest('created_at');
    }

    public function dailyReportTimes()
    {
        return $this->morphMany(DailyReportTime::class, 'reportable');
    }

    public function creator()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

    public function dealMeasurement()
    {
        return $this->belongsTo(DealMeasurement::class, 'deal_measurement_id');
    }

    public function measurement()
    {
        return $this->dealMeasurement();
    }

    public function leadProductList()
    {
        return $this->belongsTo(LeadProductList::class, 'lead_product_list_id');
    }

    public function leadStage()
    {
        return $this->belongsTo(LeadStage::class, 'lead_stage_id');
    }

    public function leadStageSubStage()
    {
        return $this->belongsTo(LeadStageSubStage::class, 'lead_stage_sub_stage_id');
    }

    public function getStartDateTimeAttribute()
    {
        if (!$this->start_date || !$this->start_time) {
            return null;
        }

        return \Carbon\Carbon::parse(
            \Carbon\Carbon::parse($this->start_date)->format('Y-m-d') . ' ' . $this->start_time
        );
    }

    public function reminderLogs()
    {
        return $this->hasMany(MainAppointmentReminderLog::class, 'appointment_id');
    }
}
