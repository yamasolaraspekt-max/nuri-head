<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetSet extends Model
{
    protected $fillable = ['asset_id','master_id','name','count','total_price'];

    protected $casts = [
        'count'       => 'integer',
        'total_price' => 'decimal:2',
    ];

    public function asset()
    {
        return $this->belongsTo(Assets::class);
    }

    public function master()
    {
        return $this->belongsTo(ProductMasterSet::class, 'master_id');
    }
}
