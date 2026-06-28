<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeePostcodeList extends Model
{
    use HasFactory;

    protected $fillable = ['employee_id', 'postcode_from', 'postcode_to', 'country'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
