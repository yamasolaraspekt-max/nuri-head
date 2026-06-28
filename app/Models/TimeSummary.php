<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeSummary extends Model
{

  protected $table = 'time_summaries';
    protected $guarded = [];

    
  protected $fillable = [
    'customer_id','alternative_id','product_id','section_id','scope','phase_id',
    'plan_minutes','actual_minutes','diff_minutes','completed_cap_minutes',
    'weighted_percent','activities_count','done_activities_count',
    'half_activities_count','overruns_count','latest_done_date'
  ];
}