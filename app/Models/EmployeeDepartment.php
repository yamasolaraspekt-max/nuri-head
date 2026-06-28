<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeDepartment extends Model
{
    use HasFactory;
      protected $fillable = [
        'employee_id',
        'department_id',
    ];

    public function department()
  {
      return $this->belongsTo(\App\Models\Department::class, 'department_id');
  }

}
