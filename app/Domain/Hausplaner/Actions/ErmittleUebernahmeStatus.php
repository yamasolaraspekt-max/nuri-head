<?php

namespace App\Domain\Hausplaner\Actions;

use App\Domain\Hausplaner\Models\HausplanerDocument;
use App\Models\Anforderungsprofil;
use App\Models\LeadAlternativeAdd;
use App\Services\BuildingModel\CanonicalHash;

/**
 * W-A — Staleness-Status der Szene-Übernahme (NUR lesend, keine Seiteneffekte).
 *
 * EINE Wahrheitsquelle: der bei der Übernahme (UebernehmeSzeneInAuslegung) unter
 * `gebaeude_geometrie._herkunft.source_hash` festgehaltene kanonische Szenen-Hash der
 * aktiven Anforderungsprofil-Version, verglichen mit dem Hash der AKTUELLEN Szene
 * (hausplaner_documents.scene_json, identische Hash-Funktion CanonicalHash::of).
 *
 * Drei Zustände (Spec W-A §1):
 *   nie      → kein aktives Profil mit Hausplaner-Herkunft (noch nie übernommen)
 *   aktuell  → Hash der aktuellen Szene == übernommener source_hash
 *   veraltet → Szene wurde seit der Übernahme geändert (Hash weicht ab)
 *
 * Keine Migration nötig: die Quell-Referenz liegt bereits an der erzeugten Version
 * (anforderungsprofile.gebaeude_geometrie._herkunft.source_hash, P2-2). Diese Klasse
 * berechnet den Vergleich an GENAU EINER Stelle — keine zweite Statusquelle.
 */
class ErmittleUebernahmeStatus
{
    public const NIE = 'nie';

    public const AKTUELL = 'aktuell';

    public const VERALTET = 'veraltet';

    /**
     * @return array{status: string, szene_revision: int|null, profil_version: int|null}
     */
    public function ausfuehren(LeadAlternativeAdd $objekt, ?HausplanerDocument $dokument): array
    {
        // Dieselbe Auswahlregel wie UebernehmeSzeneInAuslegung (Ein-aktiv-Invariante des
        // AnforderungsprofilService ⇒ first() ist eindeutig).
        $aktiv = Anforderungsprofil::query()->aktiv()
            ->where('verankerbar_type', $objekt->getMorphClass())
            ->where('verankerbar_id', $objekt->getKey())
            ->first();

        $herkunft = ($aktiv !== null && is_array($aktiv->gebaeude_geometrie))
            ? ($aktiv->gebaeude_geometrie['_herkunft'] ?? null)
            : null;

        if (($herkunft['quelle'] ?? null) !== 'hausplaner_szene' || empty($herkunft['source_hash'])) {
            return ['status' => self::NIE, 'szene_revision' => null, 'profil_version' => null];
        }

        $szene = $dokument?->scene_json;
        $aktuell = is_array($szene) && CanonicalHash::of($szene) === $herkunft['source_hash'];

        return [
            'status' => $aktuell ? self::AKTUELL : self::VERALTET,
            'szene_revision' => $dokument !== null ? (int) $dokument->revision : null,
            'profil_version' => (int) $aktiv->version,
        ];
    }
}
