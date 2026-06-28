<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\EmployeeTimeSchedule;

class Employee extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $fillable = [
        'remaining_day',
        'title',
        'name',
        'midname',
        'lastname',
        'nationality',
        'country_id',
        'contract_type_id',
        'contract_date',
        'working_hour',
        'working_type',
        'branch',
        'leave',
        'iban',
        'insurance_id',
        'remaining_day',
        'sick_leave',
        'sick_leave_remaining',
        'salary_per_hour',
        'dob',
        'bio',
        'phone',
        'email',
        'home_phone',
        'work_phone',
        'gender',
        'marital_status',
        'remaining_day',
        'health_insurance'
        ,
        'bank_name',
        'tax_id',
        'tax_class',
        'pension_no',
        'religion',
        'resident_permit',
        'resident_permit_end_date',
        'work_permit',
        'work_permit_end_date',
        'termination',
        'resignation',
        'trainee',
        'trainee_start_date',
        'trainee_end_date',
        'kids',
        'mother-tongue',
        'place_birth',
        'color',
        'status_msg',
        'daily_start_time',
        'daily_end_time'
    ];

    protected $hidden = [
        'passcode',
    ];

    public function masterSetCarts()
    {
        return $this->hasMany(\App\Models\MasterSetCart::class, 'creator_id');
    }
    public function prablem()
    {
        $this->belongsTo('App\Models\Problem');
    }

    public function comment()
    {
        $this->belongsTo('App\Models\ProblemComment');
    }


    public function language()
    {
        return $this->belongsToMany('App\Models\Languages');
    }

    public function department()
    {
        return $this->belongsToMany('App\Models\Department');
    }

    public function position()
    {
        return $this->belongsToMany('App\Models\Position');
    }

    public function licenseTypes()
    {
        return $this->belongsToMany(LicenseType::class, 'employee_license_license_type')
            ->withPivot('grade')
            ->withTimestamps();
    }
    public function contactPersonActivities()
    {
        return $this->hasMany(PhaseActivities::class, 'contact_person');
    }

    public function responsibleActivities()
    {
        return $this->hasMany(PhaseActivities::class, 'responsible_person');
    }

    public function insideServiceActivities()
    {
        return $this->hasMany(PhaseActivities::class, 'inside_service');
    }

    public function outsideServiceActivities()
    {
        return $this->hasMany(PhaseActivities::class, 'outside_service');
    }

    public function appointments()
    {
        return $this->belongsToMany(Appointment::class, 'activity_employees', 'employee_id', 'phase_id')
            ->withPivot('activity_id')
            ->withTimestamps();
    }

    public function orderedBy()
    {
        return $this->belongsTo(Employee::class, 'order_by');
    }

    public function commissionedBy()
    {
        return $this->belongsTo(Employee::class, 'commissioned_by');
    }

    public function checkedBy()
    {
        return $this->belongsTo(Employee::class, 'checked_by');
    }

    public function assembledBy()
    {
        return $this->belongsTo(Employee::class, 'assembled_by');
    }

    public function personalTasks()
    {
        return $this->belongsToMany(PersonalTask::class, 'employees_personal_tasks', 'employee_id', 'task_id')
            ->withPivot('status', 'reason', 'note', 'change_date', 'changed_by', 'change_reason');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function ticketTasks()
    {
        return $this->hasMany(TicketTask::class);
    }


    public function problems()
    {
        return $this->belongsToMany(Problem::class, 'employee_problem', 'employee_id', 'problem_id');
    }


    public function reports()
    {
        return $this->hasMany(TicketReport::class);
    }

    public function commentsWritten()
    {
        return $this->hasMany(TicketReportComment::class, 'comment_by');
    }

    public function likesGiven()
    {
        return $this->hasMany(TicketReportComment::class, 'liked_by');
    }

    public function comments()
    {
        return $this->hasMany(ProblemComment::class);
    }


    public function employeeDepartments()
    {
        return $this->hasMany(\App\Models\EmployeeDepartment::class, 'employee_id');
    }

    public function departmentPositions()
    {
        return $this->hasMany(DepartmentPosition::class, 'employee_id');
    }

    public function mainDepartmentPosition()
    {
        return $this->hasOne(DepartmentPosition::class, 'employee_id')->where('main', 'Yes');
    }


    public function createdCustomerNotes()
    {
        return $this->hasMany(CustomerNote::class, 'created_by');
    }


    public function directedCustomerNotes()
    {
        return $this->hasMany(CustomerNote::class, 'directed_to');
    }

    public function departments()
    {
        return $this->belongsToMany(
            Department::class,
            'department_positions',   // <- wichtig: NICHT department_employee
            'employee_id',
            'department_id'
        )->withPivot([
                    'position_id',
                    'percent',
                    'montage_percent',
                    'office_percent',
                    'working_hours',
                    'main',
                ])->withTimestamps();
    }



    public function offerComments()
    {
        return $this->hasMany(OfferComment::class, 'employee_id');
    }

    public function dailyReportTimes()
    {
        return $this->hasMany(DailyReportTime::class);
    }

    public function profitabilityCalculations()
    {
        return $this->hasMany(ProfitabilityCalculation::class, 'employee_id');
    }


    public function customerReports()
    {
        return $this->hasMany(CustomerReport::class, 'report_by');
    }

    public function suggestedForPhases()
    {
        return $this->hasMany(\App\Models\CustomerSuggestEmployee::class, 'employee_id');
    }

    public function salaries()
    {
        return $this->hasMany(\App\Models\Salary::class, 'emp_id');
    }


    public static function initials($id)
    {
        $employee = self::find($id);
        if (!$employee)
            return '';

        $nameParts = explode(' ', trim($employee->name . ' ' . $employee->lastname));
        $initials = collect($nameParts)->map(fn($part) => mb_substr($part, 0, 1))->join('');
        return strtoupper($initials);
    }


    public function handedOverAssets()
    {
        return $this->hasMany(Assets::class, 'handover_id');
    }


    public function createdOfferFolders()
    {
        return $this->hasMany(OfferFolder::class, 'created_by');
    }

    public function getDisplayNameAttribute(): string
    {
        $full = trim(($this->name ?? '') . ' ' . ($this->lastname ?? ''));
        return $this->name ?: ($full ?: '#' . $this->id);
    }

    public function recurringLeaves()
    {
        return $this->hasMany(EmployeeRecurringLeave::class);
    }


    public function departmentsSimple()
    {
        return $this->belongsToMany(Department::class, 'employee_departments', 'employee_id', 'department_id')
            ->withTimestamps();
    }

    public function fieldLeadProductLists()
    {
        // Inverse of the above: one employee → many lead_product_lists where field_employee = this id
        return $this->hasMany(\App\Models\LeadProRductList::class, 'field_employee', 'id');
    }

    public function getEmployeeUsers()
    {
        $employees = Employee::select('id', 'name', 'lastname', 'image')->get();

        return response()->json([
            'employees' => $employees,
        ]);
    }

    protected static function booted()
    {
        static::created(function (Employee $employee) {
            EmployeeTimeSchedule::createDefaultWeekForEmployee($employee);
        });
    }

    public function timeSchedules()
    {
        return $this->hasMany(EmployeeTimeSchedule::class);
    }



    public function productFavoriteLists()
    {
        return $this->hasMany(ProductFavoriteList::class, 'employee_id');
    }

    public function stampArticleLists()
    {
        return $this->hasMany(StampArticleList::class, 'employee_id');
    }


    public function uploadedMaintenanceDocuments()
    {
        return $this->hasMany(MaintenanceDocument::class, 'uploaded_by');
    }

    public function maintenanceProtocolsAsTechnician()
    {
        return $this->belongsToMany(MaintenanceProtocol::class, 'maintenance_protocol_technicians')
            ->using(MaintenanceProtocolTechnician::class)
            ->withPivot([
                'role',
                'status',
                'planned_hours',
                'actual_hours',
                'started_at',
                'finished_at',
                'meta',
            ])
            ->withTimestamps();
    }

    public function createdInvoices()
    {
        return $this->hasMany(Invoice::class, 'created_by');
    }

    public function updatedInvoices()
    {
        return $this->hasMany(Invoice::class, 'updated_by');
    }

    public function uploadedInvoiceFiles()
    {
        return $this->hasMany(InvoiceFile::class, 'uploaded_by');
    }


    public function acceptedGoodsReceipts()
    {
        return $this->hasMany(\App\Models\GoodsReceipt::class, 'accepted_by_employee_id');
    }

    public function createdGoodsReceipts()
    {
        return $this->hasMany(\App\Models\GoodsReceipt::class, 'created_by_employee_id');
    }

    public function goodsReceiptComments()
    {
        return $this->hasMany(\App\Models\GoodsReceiptComment::class, 'employee_id');
    }

    public function inventoryRequestsAsResponsible()
    {
        return $this->hasMany(InventoryRequestOut::class, 'responsible_id');
    }

    public function inventoryRequestsAsRequester()
    {
        return $this->hasMany(InventoryRequestOut::class, 'requester_id');
    }

    public function purchaseRequestsFrom()
    {
        return $this->hasMany(PurchaseRequest::class, 'request_from');
    }

    public function purchaseRequestsTo()
    {
        return $this->hasMany(PurchaseRequest::class, 'request_to');
    }

    public function createdPurchaseRequests()
    {
        return $this->hasMany(PurchaseRequest::class, 'employee_id');
    }

    public function customerReviews()
    {
        return $this->hasMany(\App\Models\CustomerReview::class, 'employee_id');
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class, 'emp_id');
    }

    public function createdLeaves()
    {
        return $this->hasMany(Leave::class, 'created_by');
    }

    public function leaveRequestsToApprove()
    {
        return $this->hasMany(Leave::class, 'request_to');
    }

    public function sickRecords()
    {
        return $this->hasMany(EmployeeSick::class, 'emp_id');
    }

    public function createdSickRecords()
    {
        return $this->hasMany(EmployeeSick::class, 'created_by');
    }

    public function machineServicesProvided()
    {
        return $this->hasMany(\App\Models\MachineService::class, 'service_by');
    }


    public function machineFaultsDetected()
    {
        return $this->hasMany(\App\Models\MachineService::class, 'fault_detected_by');
    }


    public function paidMachineInstallments()
    {
        return $this->hasMany(\App\Models\AssetInstallment::class, 'paid_by');
    }


}

