<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupSet extends Model
{
    use HasFactory;
    protected  $fillable = [
       'group_set',
       'content',
       'image',
        'set_id',
        'price',
       'status'
    ];   
    



    public function master(){
        return $this->belongsToMany('App\Models\ProductMasterSet');
    }

}
