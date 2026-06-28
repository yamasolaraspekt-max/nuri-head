<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\AuditableLead;
use App\Models\LeadProductList;
use App\Models\LeadStage;
use App\Models\LeadStageSubStage;
class Problem extends Model
{
    use HasFactory, SoftDeletes;
    use AuditableLead;
        protected $fillable = [
            'planner_id',
            'ticket_no',
            'error_code',
            'error_type',
            'customer_id',
            'alternative_id',
            'product_id',
            'start_user',
            'progress_user',
            'end_user',
            'edit_user',
            'date',
            'progress_date',
            'end_date',
            'total_time',
            'responsible',
            'first_contact',
            'problem',
            'solution',
            'progress',
            'image_id',
            'status',
            'edit_date',
            'repeated',
            'source',
            'article_name',
            'article_sn',
            'installation_date',
            'warranty_type',
            'warranty_duration',
            'warranty_remaining',
            'finance_to',
            'priority',
            'lead_product_list_id',
            'lead_stage_id',
            'lead_stage_sub_stage_id',

    ];  

        protected $casts = [
            'date'              => 'date',
            'progress_date'     => 'date',
            'end_date'          => 'date',
            'edit_date'         => 'date',
            'installation_date' => 'date',
            'lead_product_list_id' => 'integer',
            'lead_stage_id' => 'integer',
            'lead_stage_sub_stage_id' => 'integer',
        ];

       
        public function customer()
        {
            return $this->belongsTo(NewLeads::class, 'customer_id');
        }

        public function alternative()
        {
            return $this->belongsTo(LeadAlternativeAdd::class, 'alternative_id');
        }

        public function product()
        {
            return $this->belongsTo(ArticleGroup::class, 'product_id');
        }

        public function responsibleEmployee()
        {
            return $this->belongsTo(Employee::class, 'responsible');
        }

        public function firstContactEmployee()
        {
            return $this->belongsTo(Employee::class, 'first_contact');
        }

        public function startUser()
        {
            return $this->belongsTo(Employee::class, 'start_user');
        }

        public function progressUser()
        {
            return $this->belongsTo(Employee::class, 'progress_user');
        }

        public function endUser()
        {
            return $this->belongsTo(Employee::class, 'end_user');
        }

        public function editUser()
        {
            return $this->belongsTo(Employee::class, 'edit_user');
        }

        // 🔗 Pivot Relationships
        public function error()
        {
            return $this->belongsToMany(Error::class); // ensure pivot table exists
        }

        public function employee()
        {
            return $this->belongsToMany(Employee::class); // for additional responsible persons
        }


        public function ticketTasks()
        {
            return $this->hasMany(TicketTask::class, 'ticket_id');
        }

        public function ticket_tasks()
        {
            return $this->hasMany(TicketTask::class, 'ticket_id');
        }
        public function errors()
        {
            return $this->belongsToMany(Error::class, 'error_problem', 'problem_id', 'error_id');
        }


        public function employees()
        {
            return $this->belongsToMany(Employee::class, 'employee_problem', 'problem_id', 'employee_id');
        }

        public function comments()
        {
            return $this->hasMany(ProblemComment::class, 'ticket_id');
        }

        public function firstContact()
        {
            return $this->belongsTo(Employee::class, 'first_contact');
        }


        public function dailyReportTimes()
        {
            return $this->morphMany(DailyReportTime::class, 'reportable');
        }
         public function appointments()
        {
            return $this->hasMany(MainAppointment::class, 'problem_id');
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


}
