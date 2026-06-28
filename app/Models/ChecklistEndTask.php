<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistEndTask extends Model
{
    use HasFactory;
    protected $fillable = [
    'checklist_id',
    'phase_id',
    'activity_id',
    'done_date',
    'checked',
    'checked_date',
    'checked_by'
];

public function checklist() {
    return $this->belongsTo(Checklist::class);
}

public function phase() {
    return $this->belongsTo(TaskPhase::class);
}

public function activity() {
    return $this->belongsTo(PhaseActivity::class);
}

public function checkedBy() {
    return $this->belongsTo(Employee::class, 'checked_by');
}


}
