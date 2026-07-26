<?php

namespace App\Domain\Hausplaner\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * AUF-81 — ein gespeichertes Konfigurator-Paket.
 *
 * **Der Besitzer ist Teil des Datensatzes, nicht der Anzeige.** Ohne `user_id` gäbe es kein
 * Eigentumsgatter; jede Abfrage schränkt darauf ein, **bevor** etwas geladen wird — eine Liste,
 * die alles lädt und die Hälfte ausblendet, ist bereits geleakt.
 *
 * `alternative_id` ist **nullable**: der Konfigurator läuft autark, ohne Gebäude.
 */
class HausplanerConfiguratorPackage extends Model
{
    protected $table = 'hausplaner_configurator_packages';

    protected $fillable = [
        'user_id', 'alternative_id', 'art', 'titel', 'status', 'schema_version', 'paket',
    ];

    protected $casts = [
        'paket' => 'array',
        'schema_version' => 'integer',
        'user_id' => 'integer',
        'alternative_id' => 'integer',
    ];

    /**
     * Das Eigentumsgatter als Abfrage-Einschränkung — **serverseitig**, nicht in der Darstellung.
     * Jeder Zugriffsweg dieses Postens geht hier hindurch.
     */
    public function scopeVonNutzer($query, ?int $userId)
    {
        // Ohne Nutzer gibt es nichts zu sehen. `whereRaw('1=0')` wäre eine Rohabfrage; `whereKey`
        // auf einen unmöglichen Wert bliebe ein Sonderfall — deshalb der ehrliche Weg: ein
        // Nutzer, den es nicht gibt, sieht nichts, weil `user_id` niemals NULL ist.
        return $query->where('user_id', $userId ?? 0);
    }
}
