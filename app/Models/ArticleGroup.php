<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArticleGroup extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable=[ 'article_group', 'initial', 'image', 'min_value', 'max_value'];

     public function leads()
    {
        return $this->belongsToMany(NewLeads::class, 'lead_product_lists', 'product_id', 'customer_id')
                    ->withPivot('status')
                    ->withTimestamps();
    }

      public function taskPhases()
    {
        return $this->hasMany(TaskPhase::class, 'product_id');
    }


    public function subGroups()
    {
        return $this->hasMany(SubArticleGroup::class);
    }
    public function phaseActivities()
{
    return $this->hasMany(PhaseActivities::class, 'product_id');
}

    public function positions()
    {
        return $this->hasMany(ProductPosition::class);
    }
    public function taskDocuments()
    {
        return $this->hasMany(TaskDocument::class, 'product_id');
    }

      public function pVLongChecklists()
    {
        return $this->hasMany(PVLongChecklist::class, 'product_id');
    }
    public function pVRoofPlans()
    {
        return $this->hasMany(PVRoofPlan::class, 'product_id');
    }

     public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

     public function problems()
    {
        return $this->hasMany(Problem::class, 'product_id');
    }


    public function formulas()
    {
        return $this->hasMany(ProductFormula::class, 'product_id');
    }
    
    public function customerNotes()
    {
        return $this->hasMany(CustomerNote::class, 'product_id');
    }


    public function checklistValues()
    {
        return $this->hasMany(LeadProductChecklistValue::class, 'product_id');
    }

    public function offerComments()
    {
        return $this->hasMany(OfferComment::class, 'product_id');
    }
    
    public function cardNotes()
    {
        return $this->hasMany(CustomerCardNote::class, 'product_id');
    }
    
    public function profitabilityCalculations()
    {
        return $this->hasMany(ProfitabilityCalculation::class, 'product_id');
    }
    public function profitabilityData()
    {
        return $this->hasMany(ProfitabilityData::class, 'product_id');
    }
    
    public function reports()
    {
        return $this->hasMany(CustomerReport::class, 'product_id');
    }

    public function chats() {
        return $this->hasMany(Chat::class, 'product_id');
    }
    public function suggestedEmployees()
    {
        return $this->hasMany(\App\Models\CustomerSuggestEmployee::class, 'product_id');
    }

     public function assets()
    {
        return $this->hasMany(Assets::class, 'used_for'); // non-standard FK name
    }
    public function offerFolders()
    {
        return $this->hasMany(OfferFolder::class, 'product_id');
    }

    
    public function getDisplayNameAttribute(): string
    {
        return $this->article_group ?: ($this->article_group ?: '#'.$this->id);
    }

    public function subArticleGroups()
    {
        return $this->hasMany(SubArticleGroup::class);
    }


    public function phaseSections()
    {
        return $this->hasMany(PhaseSection::class, 'product_id');
    }

    protected static function booted()
    {
        static::created(function (ArticleGroup $group) {
            $defaults = [
                'complete',
                'montage',
                'product',
                'plan',
                'maintenance',
                'repair',
                'reclaim',
                'others',
            ];

            foreach ($defaults as $name) {
                $group->phaseSections()->create([
                    'phase_section' => $name,
                    'status'        => 'Published', // or whatever fits your app
                ]);
            }
        });
    }
 
    public function masterSets()
    {
        return $this->hasMany(\App\Models\MasterSet::class, 'article_group_id');
    }

    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class, 'product_id');
    }

    public function goodsReceipts()
    {
        return $this->hasMany(\App\Models\GoodsReceipt::class, 'product_id');
    }

    public function masterSetCarts()
    {
        return $this->hasMany(\App\Models\MasterSetCart::class, 'article_group_id');
    }


    public function offerTemplates()
    {
        return $this->hasMany(\App\Models\OfferTemplate::class, 'article_group_id');
    }

    public function customerReviews()
    {
        return $this->hasMany(\App\Models\CustomerReview::class, 'product_id');
    }

}
