<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferAssetList extends Model
{
    protected $fillable = [
        'offer_id','offer_folder_id','master_set_id',
        'asset_id','name','rate','qty','sum_total','sort_order'
    ];

    public function offer()       { return $this->belongsTo(Offer::class); }
    public function folder()      { return $this->belongsTo(OfferFolder::class,'offer_folder_id'); }
    public function masterSet()   { return $this->belongsTo(ProductMasterSet::class); }
    public function asset()       { return $this->belongsTo(Assets::class); }
}