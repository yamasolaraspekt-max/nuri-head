<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AddEmployeeToProject extends Model
{
    //

    use HasFactory;

    protected $fillable = [
        'project_id',
        'employee_id',
        'phase_id',
        'activity_id',
        'member_type',
        'status',
        'reason'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

}
