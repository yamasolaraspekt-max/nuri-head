<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\AuditableLead;

class LeadObjectRoom extends Model
{
    use HasFactory;
    use SoftDeletes;
    use AuditableLead;

    protected $table = 'lead_object_rooms';

    protected $fillable = [
        'customer_id',
        'alternative_id',

        'name',
        'area',
        'heating',
        'windows',
        'outer_wall',
        'target_temp',
        'door',
        'note',
    ];

    protected $casts = [
        'customer_id'    => 'integer',
        'alternative_id' => 'integer',
        'area'           => 'decimal:2',
        'windows'        => 'integer',
        'target_temp'    => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(NewLeads::class, 'customer_id');
    }

    public function lead()
    {
        return $this->belongsTo(NewLeads::class, 'customer_id');
    }

    public function alternative()
    {
        return $this->belongsTo(LeadAlternativeAdd::class, 'alternative_id');
    }

    public function object()
    {
        return $this->belongsTo(LeadAlternativeAdd::class, 'alternative_id');
    }

    public function newLead()
    {
        return $this->belongsTo(NewLeads::class, 'customer_id');
    }

    public function leadAlternativeAdd()
    {
        return $this->belongsTo(LeadAlternativeAdd::class, 'alternative_id');
    }
}