<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\AuditableLead;
class CustomerContactPerson extends Model
{
    use SoftDeletes;
    use AuditableLead;

    protected $table = 'customer_contact_people';

    protected $fillable = [
        'customer_id',
        'alternative_id',
        'relation',
        'name',
        'lastname',
        'phone',
        'office',
        'home',
        'email',
        'status',
    ];

    public function customer()
    {
        return $this->belongsTo(NewLeads::class, 'id');
    }

    public function alternative()
    {
        return $this->belongsTo(LeadAlternativeAdd::class, 'alternative_id');
    }

    public function getFullNameAttribute(): string
    {
        return trim(($this->name ?? '') . ' ' . ($this->lastname ?? ''));
    }
}
