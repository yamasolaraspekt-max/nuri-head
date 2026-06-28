<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Error extends Model
{
    use HasFactory;
    protected $fillable = [
        'error_code', 
        'problem_types',
        'reason',
        'solution',
        'file',
        'status',
        'product_id',
        'brand_id',
        'employee_id',
        'article_name',
        'serial_no',
        'links',
        'latest_update',
    ];

    public function problem(){
        $this->belongsTo('App\Models\Problem');
    }

    public function responsible(){
        $this->belongsTo('App\Models\Employee');
    }

    public function employee(){
        $this->belongsTo(Employee::class);
    }


      public function brands(){
        $this->belongsTo(Brand::class);
    }
    
    public function ticketTasks()
    {
        return $this->hasMany(TicketTask::class);
    }

    public function problems()
        {
            return $this->belongsToMany(Problem::class, 'error_problem', 'error_id', 'problem_id');
        }


}
