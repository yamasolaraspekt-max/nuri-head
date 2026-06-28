<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LicenseType extends Model
{
    use HasFactory;

        protected $fillable = ['type', 'grade'];
 
     public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_license_license_type')
                    ->withPivot('grade')
                    ->withTimestamps();
    }
}
