<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 
use App\Traits\AuditableLead;

class NewLeads extends Model
{
    use HasFactory, SoftDeletes;
    use AuditableLead;

   protected $fillable = [
        'customer_no',
        'customer_type',
        'title',
        'academic_title',
        'firma',
        'lastname',
        'name',
        'street',
        'latitude',
        'longitude',
        'polygon_height',
        'polygon_width',
        'polygon_area',
        'elevation',
        'postcode',
        'city',
        'phone',
        'telephone',
        'email',
        'alternative_address',
        'street2',
        'latitude2',
        'longitude2',
        'elevation2',
        'postcode2',
        'city2',
        'source',
        'request_date',
        'info',
        'document',
        'contact_person', 
        'interest_rating',
        'seriousness_rating',
        'price_information',
        'status',
        'status_msg',
        'branch',
        'full_address',
        'purchase_status',
        'purchase_date',
        'default_project_minutes',
        'delete_reason',
        'deleted_by',
        'deleted_reason_at',
        'junk_reason',
        'junked_by',
        'junked_at',
        'unjunk_reason',
        'unjunked_by',
        'unjunked_at',
    ];

    protected $casts = [
        'total_purchase' => 'decimal:2', 
        'deleted_reason_at' => 'datetime',
        'junked_at' => 'datetime',
        'unjunked_at' => 'datetime',
    ];


     public function alternativeAddresses()
    {
        return $this->hasMany(LeadAlternativeAdd::class, 'lead_id', 'id');
    }

    public function products()
    {
        return $this->belongsToMany(
            ArticleGroup::class,
            'lead_product_lists',
            'customer_id',
            'product_id'
        )->withPivot('service', 'status', 'work_status');
    }


    public function responsibilities()
    {
        return $this->hasMany(NewLeadResponsibility::class, 'new_lead_id', 'id');
    }
    
    public function category()
{
    return $this->belongsTo(ImageCategory::class, 'category_id', 'id');
}


 public function problems()
    {
        return $this->hasMany(Problem::class, 'customer_id');
    }


    public function solarSystems()
    {
        return $this->hasMany(SolarSystem::class);
    }

    public function heatPumps()
    {
        return $this->hasMany(HeatPump::class);
    }

    public function economicCalculations()
    {
        return $this->hasMany(EconomicCalculation::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch');
    }

    public function contact()
    {
        return $this->belongsTo(Employee::class, 'contact_person');
    }

    public function customerNotes()
{
    return $this->hasMany(CustomerNote::class, 'customer_id');
}

public function checklistValues()
{
    return $this->hasMany(LeadProductChecklistValue::class, 'customer_id');
}


public function leadProductLists()
{
    return $this->hasMany(LeadProductList::class, 'customer_id');
}


public function offerComments()
{
    return $this->hasMany(OfferComment::class, 'customer_id');
}


public function cardNotes()
{
    return $this->hasMany(CustomerCardNote::class, 'customer_id');
}

public function profitabilityCalculations()
{
    return $this->hasMany(ProfitabilityCalculation::class, 'customer_id');
}

public function profitabilityData()
{
    return $this->hasMany(ProfitabilityData::class, 'customer_id');
}

public function reports()
{
    return $this->hasMany(CustomerReport::class, 'customer_id');
}

public function chats() {
    return $this->hasMany(Chat::class, 'customer_id');
}

public function suggestedEmployees()
{
    return $this->hasMany(\App\Models\CustomerSuggestEmployee::class, 'customer_id');
}


    // calculate if the customer has purchased or not 

   protected $appends = ['has_purchased', 'tier'];

 
    public function getHasPurchasedAttribute(): bool
    {
        // normalize truthy variants you might be storing
        $val = strtolower((string) $this->purchase_status);
        return in_array($val, ['1','true','yes','ja','purchased','paid', 'customer'], true);
    }

    public function getTierAttribute(): ?string
    {
        if (!$this->has_purchased) return null;

        $total = (float) $this->total_purchase;
        if ($total < 5000)      return 'bronze';
        if ($total < 15000)     return 'silver';
        if ($total < 50000)     return 'gold';
        return 'platinum';
    }
 
    public function leadAlternativeAdds()
    {
        return $this->alternativeAddresses();
    } 
   public function personalTasks()
    {
        // personal_tasks.customer_id -> new_leads.id
        return $this->hasMany(\App\Models\PersonalTask::class, 'customer_id', 'id');
    } 
    public function addresses()     { return $this->hasMany(LeadAlternativeAdd::class, 'lead_id'); }
    public function mainAddress()   { return $this->hasOne(LeadAlternativeAdd::class, 'lead_id')->where('main', 1); }
     public function branchRel()     { return $this->belongsTo(Branch::class, 'branch'); }

     public function offerFolders()
    {
        return $this->hasMany(OfferFolder::class, 'customer_id');
    }

    public function getDisplayNameAttribute(): string
    {
        $full = trim(($this->name ?? '').' '.($this->lastname ?? ''));

        if (!empty($this->firma)) {
            return $this->firma;
        }

        if (!empty($full)) {
            return $full;
        }

        if (!empty($this->customer_no)) {
            return '#'.$this->customer_no;
        }

        return '#'.$this->id;
    }

     public function reportTimes()
    {
        return $this->belongsToMany(DailyReportTime::class, 'daily_report_time_customers', 'customer_id', 'report_time_id')
            ->withPivot(['share_hours','share_percent'])
            ->withTimestamps();
    }

    public function contactPerson()
    {
        return $this->belongsTo(\App\Models\Employee::class, 'contact_person');
    }

    public function branchRelation()
    {
        return $this->belongsTo(\App\Models\Branch::class, 'branch');
    }

    public function projectTimeRequests()
    {
        return $this->hasMany(ProjectTimeRequest::class, 'customer_id');
    }

    public function maintenanceContracts()
    {
        return $this->hasMany(MaintenanceContract::class, 'lead_id');
    }

    public function maintenanceAssets()
    {
        return $this->hasMany(MaintenanceAsset::class, 'lead_id');
    }

    public function maintenanceProtocols()
    {
        return $this->hasMany(MaintenanceProtocol::class, 'lead_id');
    }

    public function contactPeople()
    {
        return $this->hasMany(\App\Models\CustomerContactPerson::class, 'customer_id');
    }

    // Add this to App\Models\NewLeads.php
    public function alternatives()
    {
        // This points to your existing relationship
        return $this->alternativeAddresses();
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'customer_id');
    }

    public function objects()
    {
        return $this->hasMany(LeadAlternativeAdd::class, 'lead_id');
    }

    public function goodsReceiptsAsCustomer()
    {
        return $this->hasMany(\App\Models\GoodsReceipt::class, 'customer_id');
    }

    public function rooms()
    {
        return $this->hasMany(\App\Models\LeadObjectRoom::class, 'customer_id');
    }

    public function leadObjectRooms()
    {
        return $this->hasMany(\App\Models\LeadObjectRoom::class, 'customer_id');
    }

    public function reviews()
    {
        return $this->hasMany(\App\Models\CustomerReview::class, 'customer_id');
    }

}
