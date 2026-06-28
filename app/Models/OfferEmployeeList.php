<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferEmployeeList extends Model
{
    protected $fillable = [
        'offer_id','offer_folder_id','master_set_id','position_id',
        'role','rate','qty','hours_per_person','hours_total','sum_total'
    ];

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function masterSet()
    {
        return $this->belongsTo(ProductMasterSet::class, 'master_set_id');
    }
}
