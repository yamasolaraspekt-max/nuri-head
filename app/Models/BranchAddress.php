<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BranchAddress extends Model
{
      use HasFactory, SoftDeletes; 

            protected $fillable =  [
                  'branch_id', 'employee_id', 'name', 'full_address', 'street', 'latitude', 'longitude', 'postcode', 'city', 'phone', 'telephone', 'email', 'status'
            ];
}
