<?php

namespace App\Domain\Hausplaner\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Hausplaner P0 — historischer Planstand, append-only (nie editieren; Wiederherstellen = neue Revision). */
class HausplanerSnapshot extends Model
{
    protected $table = 'hausplaner_snapshots';

    protected $fillable = [
        'hausplaner_document_id', 'revision', 'scene_json', 'label', 'reason', 'created_by',
    ];

    protected $casts = [
        'revision' => 'integer',
        'scene_json' => 'array',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(HausplanerDocument::class, 'hausplaner_document_id');
    }
}
