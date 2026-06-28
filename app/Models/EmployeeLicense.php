<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeLicense extends Model
{
    use HasFactory;

    protected $fillable=[
        'emp_id',
        'type', 
        'license_no',
        'expiry_date',
        'grade',
        'status',
        'image'
    ];

 



    public function type(){
        return $this->belongsToMany('App\Models\LicenseType');
    }

}
