<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

use App\Traits\AuditableLead;
use App\Models\LeadStage;
use App\Models\Stage;
use App\Models\TaskPhase;
use App\Models\PhaseSection;
use App\Models\ArticleGroup;
use App\Models\LeadAlternativeAdd;
use App\Models\Employee;
use App\Models\Department;
use App\Models\MaintenanceAsset;
use App\Models\LeadProductChecklistValue;
use App\Models\NewLeads;
use App\Models\OfferFolder;

class LeadProductList extends Model
{
    use HasFactory;
    use SoftDeletes;
    use AuditableLead;

    protected $table = 'lead_product_lists';

    protected $fillable = [
        'customer_id',
        'alternative_id',
        'product_id',
        'service_id',
        'department_id',
        'employee_id',
        'field_employee',
        'service',
        'status',

        'stage_mode',
        'product_stage_id',
        'product_task_phase_id',

        'work_status',
        'interest',
        'realization_time',
        'stage_history',
        'stage',
        'old_stage',
        'price',
        'price_latest',
        'project_minutes',
        'price_history',
        'net_amount',
        'gross_amount',
        'tax_amount',
        'receipt_date',
        'receipt_reference',
        'teams',
        'lead_stage_sub_stage_id',

        // Offer acceptance / bypass tracking
        'accepted_offer_folder_id',
        'offer_acceptance_status',
        'moved_without_offer_acceptance',
        'moved_without_offer_acceptance_at',
        'moved_without_offer_acceptance_by',
        'moved_without_offer_acceptance_reason',

        'deleted_at',
    ];

    protected $casts = [
        'stage_history' => 'array',
        'price_history' => 'array',
        'teams' => 'array',

        'stage_mode' => 'string',
        'product_stage_id' => 'integer',
        'product_task_phase_id' => 'integer',

        'price' => 'string',
        'price_latest' => 'string',
        'project_minutes' => 'integer',
        'net_amount' => 'decimal:2',
        'gross_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'receipt_date' => 'date',
        'lead_stage_sub_stage_id' => 'integer',

        // Offer acceptance / bypass tracking
        'accepted_offer_folder_id' => 'integer',
        'offer_acceptance_status' => 'string',
        'moved_without_offer_acceptance' => 'boolean',
        'moved_without_offer_acceptance_at' => 'datetime',
        'moved_without_offer_acceptance_by' => 'integer',

        'deleted_at' => 'datetime',
    ];

    /** Stufe B: key->id-Map der Lead-Stages, statisch je Request gecacht (Hook + raw-Insert-Fix). */
    protected static ?array $stageIdByKeyCache = null;

    /**
     * Stufe B: zentrales Netz fuer die FK-Wahrheit lead_stage_id. Deckt ALLE Eloquent-Schreiber.
     * Raw DB::table-Writer umgehen den Hook -> die (eine) Batch-INSERT direkt gefixt; raw-UPDATEs
     * faengt der B1-Query-Fold + Log. changeStage setzt beide -> FK dirty -> Hook greift nicht ein.
     */
    protected static function booted(): void
    {
        static::creating(function (self $lead) {
            if (empty($lead->lead_stage_id)) {
                $id = static::deriveLeadStageId($lead->status);
                if ($id !== null) {
                    $lead->lead_stage_id = $id;
                } else {
                    Log::warning('lead_stage_id derive-miss (creating)', ['status' => $lead->status]);
                }
            }
        });

        static::updating(function (self $lead) {
            // Stale-FK-Schutz: status geaendert, FK NICHT selbst gesetzt -> FK aus NEUEM status neu ableiten
            // (gefoldet, ueberschreibt stale). changeStage setzt beide -> lead_stage_id dirty -> Hook greift nicht ein.
            if ($lead->isDirty('status') && !$lead->isDirty('lead_stage_id')) {
                $id = static::deriveLeadStageId($lead->status);
                if ($id !== null) {
                    $lead->lead_stage_id = $id;
                } else {
                    // Unbekannter Key: FK unveraendert lassen, kein Crash.
                    Log::warning('lead_stage_id derive-miss (updating)', ['status' => $lead->status]);
                }
            }
        });
    }

    /**
     * Ableitung lead_stage_id aus status, GEFOLDET nach Backfill-Kanon (follow_up->offer, accepted->deal).
     * Unbekannter Key -> null. key->id-Map statisch je Request gecacht.
     */
    public static function deriveLeadStageId(?string $status): ?int
    {
        $key = strtolower(trim((string) $status));
        if ($key === '' || $key === 'open') {
            $key = 'lead';
        }

        $syn = [
            'new' => 'lead', 'neu' => 'lead', 'neue' => 'lead',
            'angebot' => 'offer', 'verkauf' => 'offer',
            'nachfassen' => 'follow_up', 'followup' => 'follow_up',
            'annehmen' => 'accepted', 'annemen' => 'accepted', 'angenommen' => 'accepted',
            'auftrag' => 'deal',
            'montage' => 'project', 'projekt' => 'project',
            'abschluss' => 'completed', 'complete' => 'completed',
            'archiv' => 'archive', 'reject' => 'junk', 'rejeck' => 'junk',
        ];
        $key = $syn[$key] ?? $key;

        // FOLD auf die Eltern-Phase (Kanon aus 169d1ee / B1-Query-Fold).
        $fold = ['follow_up' => 'offer', 'accepted' => 'deal'];
        $key = $fold[$key] ?? $key;

        if (static::$stageIdByKeyCache === null) {
            static::$stageIdByKeyCache = LeadStage::query()
                ->pluck('id', 'key')
                ->mapWithKeys(fn($id, $k) => [strtolower(trim((string) $k)) => (int) $id])
                ->toArray();
        }

        return static::$stageIdByKeyCache[$key] ?? null;
    }

    public function customer()
    {
        return $this->belongsTo(NewLeads::class, 'customer_id');
    }

    public function checklistValues()
    {
        return $this->hasMany(LeadProductChecklistValue::class, 'lead_product_list_id');
    }

    public function product()
    {
        return $this->belongsTo(ArticleGroup::class, 'product_id', 'id');
    }

    public function articleGroup()
    {
        return $this->belongsTo(ArticleGroup::class, 'product_id', 'id');
    }

    public function alternative()
    {
        return $this->belongsTo(LeadAlternativeAdd::class, 'alternative_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    public function fieldEmployee()
    {
        return $this->belongsTo(Employee::class, 'field_employee', 'id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }

    public function maintenanceAssets()
    {
        return $this->hasMany(MaintenanceAsset::class, 'lead_product_list_id');
    }

    public function service()
    {
        return $this->belongsTo(PhaseSection::class, 'service_id', 'id');
    }

    public function reviews()
    {
        return $this->hasMany(\App\Models\CustomerReview::class, 'product_id', 'product_id')
            ->where('customer_id', $this->customer_id)
            ->where('alternative_id', $this->alternative_id);
    }

    public function companyStage()
    {
        return $this->belongsTo(LeadStage::class, 'status', 'key');
    }

    public function productStage()
    {
        return $this->belongsTo(Stage::class, 'product_stage_id', 'id');
    }

    public function productTaskPhase()
    {
        return $this->belongsTo(TaskPhase::class, 'product_task_phase_id', 'id');
    }

    public function reminders()
    {
        return $this->hasMany(\App\Models\LeadReminder::class, 'lead_product_list_id');
    }

    public function leadStageSubStage()
    {
        return $this->belongsTo(\App\Models\LeadStageSubStage::class, 'lead_stage_sub_stage_id');
    }

    public function acceptedOfferFolder()
    {
        return $this->belongsTo(OfferFolder::class, 'accepted_offer_folder_id');
    }

    public function movedWithoutOfferAcceptanceByEmployee()
    {
        return $this->belongsTo(Employee::class, 'moved_without_offer_acceptance_by', 'id');
    }

    public function kanbanTasks()
    {
        return $this->hasMany(\App\Models\KanbanLeadTask::class, 'lead_product_list_id');
    }

    public function plannerPlans()
    {
        return $this->hasMany(\App\Models\PlannerPlan::class, 'project_id');
    }

    public function latestPlannerPlan()
    {
        return $this->hasOne(\App\Models\PlannerPlan::class, 'project_id')->latestOfMany();
    }

    public function appointments()
    {
        return $this->hasMany(\App\Models\MainAppointment::class, 'lead_product_list_id');
    }

    public function personalTasks()
    {
        return $this->hasMany(\App\Models\PersonalTask::class, 'lead_product_list_id');
    }

    public function tickets()
    {
        return $this->hasMany(\App\Models\Problem::class, 'lead_product_list_id');
    }

    public function plannerItems()
    {
        return $this->hasManyThrough(
            \App\Models\PlannerItem::class,
            \App\Models\PlannerPlan::class,
            'project_id',
            'plan_id',
            'id',
            'id'
        );
    }

    public function servicePhase()
    {
        return $this->belongsTo(PhaseSection::class, 'service_id', 'id');
    }
}
