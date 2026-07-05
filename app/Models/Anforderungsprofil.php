<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use RuntimeException;

/**
 * B2a-2 — Anforderungsprofil (Framework Phase 1): das führende, versionierte Bedarfsobjekt der Auslegung.
 *
 * Polymorph verankert mit Whitelist (kein freier Typ-String): kanonisch am Objekt (LeadAlternativeAdd),
 * optional am Gewerk (LeadProductList). Append-only versioniert (Operationen im AnforderungsprofilService);
 * genau EINE aktive Version je Verankerung (App-Invariante). Werte in anforderungsprofil_werte (Registry-Vertrag).
 */
class Anforderungsprofil extends Model
{
    use HasFactory;

    protected $table = 'anforderungsprofile';

    protected $fillable = [
        'verankerbar_type', 'verankerbar_id', 'version', 'status',
        'abgeloest_durch_id', 'bezeichnung', 'created_by',
    ];

    protected $casts = [
        'version' => 'integer',
    ];

    public const STATUS_ENTWURF = 'entwurf';
    public const STATUS_AKTIV = 'aktiv';
    public const STATUS_ABGELOEST = 'abgeloest';

    /**
     * Morph-Whitelist statt freiem String: nur diese Typen dürfen verankern.
     * LeadAlternativeAdd = Objekt/Gebäude (kanonisch — Phase-3: "Heizlast gehört ans Objekt").
     * LeadProductList = Gewerk/Deal (vorgangsbezogene Profile). customer NICHT (zu unspezifisch).
     */
    public const ERLAUBTE_ANKER = [
        LeadAlternativeAdd::class,
        LeadProductList::class,
    ];

    protected static function booted(): void
    {
        static::saving(fn (self $profil) => $profil->verankerungPruefen());
    }

    /** Whitelist + exists-Check je Typ (die DB erzwingt polymorphe FKs nicht). */
    public function verankerungPruefen(): void
    {
        $typ = $this->verankerbar_type;

        if (! in_array($typ, self::ERLAUBTE_ANKER, true)) {
            throw new RuntimeException('Anforderungsprofil: Verankerungs-Typ nicht erlaubt: '.var_export($typ, true));
        }

        $existiert = (new $typ)->newQuery()->whereKey($this->verankerbar_id)->exists();
        if (! $existiert) {
            throw new RuntimeException("Anforderungsprofil: verankerbar_id {$this->verankerbar_id} in {$typ} existiert nicht.");
        }
    }

    public function verankerbar(): MorphTo
    {
        return $this->morphTo();
    }

    public function werte(): HasMany
    {
        return $this->hasMany(AnforderungsprofilWert::class);
    }

    public function abgeloestDurch(): BelongsTo
    {
        return $this->belongsTo(self::class, 'abgeloest_durch_id');
    }

    public function erfasser(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

    public function scopeAktiv(Builder $q): Builder
    {
        return $q->where('status', self::STATUS_AKTIV);
    }
}
