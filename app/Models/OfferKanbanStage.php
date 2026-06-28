<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class OfferKanbanStage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'document_status',
        'key',
        'label',
        'icon',
        'color',
        'position',
        'is_active',
        'is_default',
        'is_system',
        'description',
    ];

    protected $casts = [
        'position' => 'integer',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'is_system' => 'boolean',
    ];

    public const STATUS_OFFER = 'offer';
    public const STATUS_DEAL = 'deal';

    public static function normalizeDocumentStatus(?string $status): string
    {
        $status = strtolower(trim((string) $status));

        return in_array($status, ['deal', 'auftrag'], true)
            ? self::STATUS_DEAL
            : self::STATUS_OFFER;
    }

    public static function makeKey(string $label): string
    {
        $map = [
            'ä' => 'ae',
            'ö' => 'oe',
            'ü' => 'ue',
            'ß' => 'ss',
            'Ä' => 'ae',
            'Ö' => 'oe',
            'Ü' => 'ue',
        ];

        return Str::slug(strtr($label, $map), '_');
    }

    public function scopeForDocument($query, ?string $documentStatus)
    {
        return $query->where('document_status', self::normalizeDocumentStatus($documentStatus));
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function orderedFor(?string $documentStatus, bool $onlyActive = true)
    {
        return static::query()
            ->forDocument($documentStatus)
            ->when($onlyActive, fn($query) => $query->active())
            ->orderBy('position')
            ->orderBy('id')
            ->get();
    }

    public static function keyListFor(?string $documentStatus): array
    {
        return static::orderedFor($documentStatus)->pluck('key')->values()->toArray();
    }

    public static function labelMapFor(?string $documentStatus): array
    {
        return static::orderedFor($documentStatus)->pluck('label', 'key')->toArray();
    }
}
