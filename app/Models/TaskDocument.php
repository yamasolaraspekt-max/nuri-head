<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskDocument extends Model
{
   protected $fillable = [
        'customer_id',
        'alternative',
        'phase_id',
        'product_id',
        'activities_id',
        'sub_task_id',
        'document_name',
        'document', 
        'document_sum',
        'document_note',
        'document_status',
    ];

    // Define the relationships

    // Relationship with the Customer model
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Relationship with the TaskPhase model
    public function taskPhase()
    {
        return $this->belongsTo(TaskPhase::class, 'phase_id');
    }

    // Relationship with the PhaseActivity model
    public function phaseActivity()
    {
        return $this->belongsTo(PhaseActivity::class, 'activities_id');
    }

    // Relationship with the ArticleGroup model
    public function articleGroup()
    {
        return $this->belongsTo(ArticleGroup::class, 'product_id');
    }

    
}
