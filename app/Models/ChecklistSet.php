<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistSet extends Model
{
    use HasFactory;
    protected $fillable = [
    'checklist_id',
    'master_set_id',
    'designation',
    'ordered',
    'order_date',
    'order_by',
    'commisioned',
    'commisioned_date',
    'commisioned_by',
    'checked',
    'checked_date',
    'checked_by',
    'order',
    'status'
];

public function checklist() {
    return $this->belongsTo(Checklist::class);
}

public function masterSet() {
    return $this->belongsTo(ProductMasterSet::class);
}

public function designationProduct() {
    return $this->belongsTo(Product::class, 'designation');
}

public function orderedBy() {
    return $this->belongsTo(Employee::class, 'order_by');
}

public function commisionedBy() {
    return $this->belongsTo(Employee::class, 'commisioned_by');
}

public function checkedBy() {
    return $this->belongsTo(Employee::class, 'checked_by');
}


}
