<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeSet extends Model
{
    use HasFactory;
    protected $fillable = ['master_set_id','product_id','position_id','work_hour','buying_price','sale_price','total'];
    public function master()   { return $this->belongsTo(ProductMasterSet::class,'master_set_id'); }
    public function position() { return $this->belongsTo(Position::class, 'position_id'); }
    public function product()  { return $this->belongsTo(ArticleGroup::class, 'product_id'); } // role/skill
    
   
}
