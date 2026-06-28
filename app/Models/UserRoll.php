<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRoll extends Model
{
    use HasFactory;

    protected $table = 'user_rolls';

    protected $fillable = [
        'user_id',
        'item_id',
        'is_read',
        'is_update',
        'is_delete',
        'is_add',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'is_read' => 'boolean',
        'is_update' => 'boolean',
        'is_delete' => 'boolean',
        'is_add' => 'boolean',
    ];

    public const ACTION_READ = 'read';
    public const ACTION_ADD = 'add';
    public const ACTION_UPDATE = 'update';
    public const ACTION_DELETE = 'delete';

   public function employee()
{
    return $this->belongsTo(Employee::class, 'user_id', 'id');
}

    public function canRead(): bool
    {
        return (bool) $this->is_read;
    }

    public function canAdd(): bool
    {
        return (bool) $this->is_add;
    }

    public function canUpdate(): bool
    {
        return (bool) $this->is_update;
    }

    public function canDelete(): bool
    {
        return (bool) $this->is_delete;
    }

    public function allows(string $action): bool
    {
        return match ($action) {
            'read', 'view', 'show', 'index' => $this->canRead(),
            'add', 'create', 'store' => $this->canAdd(),
            'update', 'edit' => $this->canUpdate(),
            'delete', 'destroy' => $this->canDelete(),
            default => false,
        };
    }

    public static function permissionColumn(string $action): string
    {
        return match ($action) {
            'read', 'view', 'show', 'index' => 'is_read',
            'add', 'create', 'store' => 'is_add',
            'update', 'edit' => 'is_update',
            'delete', 'destroy' => 'is_delete',
            default => 'is_read',
        };
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForItem($query, string $item)
    {
        return $query->where('item_id', $item);
    }
}