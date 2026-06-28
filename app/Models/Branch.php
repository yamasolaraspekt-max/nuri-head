<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'slug',             // New
        'branch',
        'chairman',
        'image',
        'street',
        'postcode',
        'city',
        'country',
        'employee_count',
        'total_expense',
        'email',
        'phone',
        'initial',
        'color',
        'second_color',     // New
        'whatsapp',         // New
        'web',              // New
        'bank',             // New
        'iban',             // New
        'bic',              // New
        'register',         // New
        'tax',              // New
        'vat',              // New
        'gf',               // New
        'contact_person',   // New
        'logo_url'          // New
    ];

    public function addresses()
    {
        return $this->hasMany(BranchAddress::class);
    }

    public function assets()
    {
        return $this->hasMany(Assets::class, 'branch_id');
    }

    public function machines()
    {
        return $this->hasMany(\App\Models\Machine::class, 'branch_id');
    }

    public function assetInstallments()
    {
        return $this->hasMany(\App\Models\AssetInstallment::class, 'branch_id');
    }

    public function installmentPayments()
    {
        return $this->hasMany(\App\Models\InstallmentPayment::class, 'branch_id');
    }
 
    
}