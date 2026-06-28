<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

class OfferFolderActivity extends Model
{
    use HasFactory;

    /**
     * The table already exists in MySQL.
     */
    protected $table = 'offer_folder_activities';

    /**
     * Keep this open enough for your existing controller activity payloads.
     */
    protected $fillable = [
        'offer_folder_id',
        'offer_id',
        'employee_id',
        'type',
        'title',
        'message',
        'meta',
    ];

    protected $casts = [
        'offer_folder_id' => 'integer',
        'offer_id' => 'integer',
        'employee_id' => 'integer',
        'meta' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Only enable SoftDeletes if the table actually has deleted_at.
     * This prevents errors if your existing table was created without it.
     */
    public static function bootSoftDeletes(): void
    {
        if (Schema::hasColumn((new static)->getTable(), 'deleted_at')) {
            static::addGlobalScope(new \Illuminate\Database\Eloquent\SoftDeletingScope);
        }
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(OfferFolder::class, 'offer_folder_id');
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class, 'offer_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
