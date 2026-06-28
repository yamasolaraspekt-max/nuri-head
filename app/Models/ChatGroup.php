<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'avatar',
        'created_by',
        'customer_id',
        'alternative_id',
        'product_id',
        'lead_product_list_id',
    ];

    // Creator (user who owns the group, usually admin)
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Members (users.id on pivot)
    public function users()
    {
        return $this->belongsToMany(User::class, 'chat_group_user')
            ->withPivot([
                'role',
                'status',
                'invited_by',
                'joined_at',
                'history_visibility',
                'can_write',
            ])
            ->withTimestamps();
    }
    // Messages
    public function chats()
    {
        return $this->hasMany(Chat::class, 'group_id');
    }

    // CRM context
    public function customer()
    {
        return $this->belongsTo(NewLeads::class, 'customer_id');
    }

    public function alternative()
    {
        return $this->belongsTo(LeadAlternativeAdd::class, 'alternative_id');
    }

    public function product()
    {
        return $this->belongsTo(ArticleGroup::class, 'product_id');
    }

    public function leadProductLine()
    {
        return $this->belongsTo(LeadProductList::class, 'lead_product_list_id');
    }

    // Nice label for UI: “Firma / Name (Objekt – Produkt)”
    public function getContextLabelAttribute(): ?string
    {
        if (!$this->customer) {
            return null;
        }

        $parts = [];

        $custName = trim(
            ($this->customer->firma ?: '') . ' ' .
            ($this->customer->name ?: '') . ' ' .
            ($this->customer->lastname ?: '')
        );
        if ($custName !== '') {
            $parts[] = $custName;
        }

        if ($this->alternative) {
            $parts[] = $this->alternative->object_name ?: $this->alternative->city;
        }

        if ($this->product) {
            $parts[] = $this->product->article_group;
        }

        if ($this->leadProductLine) {
            $parts[] = '#'.$this->leadProductLine->id.' '.$this->leadProductLine->status;
        }

        return implode(' — ', array_filter($parts));
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'chat_group_user')
            ->withPivot(['role', 'status', 'history_visibility', 'can_write', 'is_pinned'])
            ->withTimestamps();
    }

     public function messages()
    {
        // adjust 'group_id' if your column is named differently
        return $this->hasMany(Chat::class, 'group_id');
    }
}
