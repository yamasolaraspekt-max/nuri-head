<?php

namespace App\Listeners;

use App\Events\LeadRecordChanged;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StoreLeadActivity
{
    public function handle(LeadRecordChanged $event)
    {
        $model = $event->model;
        
        // Attempt to resolve context IDs dynamically based on the Model
        $leadId = $model->customer_id ?? $model->lead_id ?? ($model instanceof \App\Models\NewLeads ? $model->id : null);
        $altId  = $model->alternative_id ?? ($model instanceof \App\Models\LeadAlternativeAdd ? $model->id : null);
        $prodId = $model->product_id ?? ($model instanceof \App\Models\ArticleGroup ? $model->id : null);

        DB::table('lead_activity_logs')->insert([
            'new_leads_id'   => $leadId,
            'alternative_id' => $altId,
            'product_id'     => $prodId,
            'user_id'        => Auth::id(),
            'user_name'      => Auth::check() ? Auth::user()->name . ' ' . Auth::user()->lastname : 'System',
            'event_type'     => $event->eventType,
            'model_type'     => get_class($model),
            'model_id'       => $model->id,
            'changes'        => json_encode($event->changes),
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);
    }
}