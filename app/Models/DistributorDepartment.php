<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistributorDepartment extends Model
{
    use HasFactory;
    protected $fillable = [
            'd_id',
            'd_department',
            'name',
            'position',
            'phone',
            'office',
            'home',
            'email',
            'status',
        ];

        // Belongs to a distributor
        public function distributor()
        {
            return $this->belongsTo(Distributor::class, 'd_id');
        }
}
