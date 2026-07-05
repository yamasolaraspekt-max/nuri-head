<?php

namespace App\Models;

use App\Services\Anforderungsprofil\SchluesselRegistry;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RuntimeException;

/**
 * B2a-2 — Anforderungsprofil-Wert (EAV): ein Bedarfswert mit seiner Datenlage-Stufe.
 *
 * Jeder Wert trägt datenlage (gemessen|berechnet|geschaetzt) + quelle + erfassungsweg — bis ins
 * Rechenergebnis durchreichbar. Beim Schreiben gegen die SchluesselRegistry validiert:
 * unbekannter schluessel / ungültige datenlage / fehlendes Pflicht-wert_num = Fehler (keine stille Zeile).
 */
class AnforderungsprofilWert extends Model
{
    use HasFactory;

    protected $table = 'anforderungsprofil_werte';

    protected $fillable = [
        'anforderungsprofil_id', 'schluessel', 'wert', 'wert_num',
        'einheit', 'datenlage', 'quelle', 'erfassungsweg',
    ];

    protected $casts = [
        'wert_num' => 'decimal:4',
    ];

    protected static function booted(): void
    {
        static::saving(fn (self $wert) => $wert->registryPruefen());
    }

    public function registryPruefen(): void
    {
        if (! SchluesselRegistry::istErlaubt((string) $this->schluessel)) {
            throw new RuntimeException("Anforderungsprofil-Wert: unbekannter schluessel '{$this->schluessel}' (nicht in Registry).");
        }

        if (! SchluesselRegistry::datenlageErlaubt((string) $this->datenlage)) {
            throw new RuntimeException("Anforderungsprofil-Wert '{$this->schluessel}': ungültige datenlage '{$this->datenlage}' (erlaubt: ".implode('|', SchluesselRegistry::DATENLAGE).').');
        }

        if (SchluesselRegistry::definition((string) $this->schluessel)['wert_num_pflicht'] && $this->wert_num === null) {
            throw new RuntimeException("Anforderungsprofil-Wert '{$this->schluessel}': wert_num ist Pflicht (Registry).");
        }
    }

    public function anforderungsprofil(): BelongsTo
    {
        return $this->belongsTo(Anforderungsprofil::class);
    }
}
