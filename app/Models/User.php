<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'is_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'is_admin' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Permissions / Roles
    |--------------------------------------------------------------------------
    */

    public function user_rolls()
    {
        return $this->hasMany(UserRoll::class, 'user_id', 'id');
    }

    // Optional alias, if some old code still calls auth()->user()->user_roll
    public function user_roll()
    {
        return $this->hasMany(UserRoll::class, 'user_id', 'id');
    }

    public function isSuperAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    public function hasPermission(string $item, string $action = 'read'): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $column = match ($action) {
            'read', 'view', 'show', 'index' => 'is_read',
            'add', 'create', 'store' => 'is_add',
            'update', 'edit' => 'is_update',
            'delete', 'destroy' => 'is_delete',
            default => 'is_read',
        };

        return $this->user_rolls()
            ->where('item_id', $item)
            ->where($column, true)
            ->exists();
    }

    /*
    |--------------------------------------------------------------------------
    | Employee
    |--------------------------------------------------------------------------
    | Important:
    | In your project, users.name seems to store employees.id.
    |--------------------------------------------------------------------------
    */

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'name', 'id');
    }

    /*
    |--------------------------------------------------------------------------
    | Chat
    |--------------------------------------------------------------------------
    */

    public function chatGroups()
    {
        return $this->belongsToMany(ChatGroup::class, 'chat_group_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function ownedChatGroups()
    {
        return $this->hasMany(ChatGroup::class, 'created_by');
    }

    public function chatsSent()
    {
        return $this->hasMany(Chat::class, 'from_user_id');
    }

    public function chatsReceived()
    {
        return $this->hasMany(Chat::class, 'to_user_id');
    }

    public function readChats()
    {
        return $this->belongsToMany(Chat::class, 'chat_reads', 'user_id', 'chat_id')
            ->withPivot('read_at');
    }

    /*
    |--------------------------------------------------------------------------
    | Preferences / Activity
    |--------------------------------------------------------------------------
    */

    public function preference()
    {
        return $this->hasOne(UserPreference::class, 'user_id');
    }

    public function activityFilter()
    {
        return $this->hasOne(UserActivityFilter::class, 'user_id');
    }
}