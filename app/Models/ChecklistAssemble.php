<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistAssemble extends Model
{
    use HasFactory;
    protected $fillable = [
    'checklist_id',
    'checklist_set_id',
    'master_set_id',
    'assembled',
    'assembled_date',
    'assembled_by'
];

public function checklist() {
    return $this->belongsTo(Checklist::class);
}

public function checklistSet() {
    return $this->belongsTo(ChecklistSet::class);
}

public function masterSet() {
    return $this->belongsTo(ProductMasterSet::class);
}

public function assembledBy() {
    return $this->belongsTo(Employee::class, 'assembled_by');
}

}
