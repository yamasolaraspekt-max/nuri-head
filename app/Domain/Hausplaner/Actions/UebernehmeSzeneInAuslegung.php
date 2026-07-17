<?php

namespace App\Domain\Hausplaner\Actions;

use App\Domain\Hausplaner\Models\HausplanerDocument;
use App\Models\Anforderungsprofil;
use App\Models\HeizlastRaum;
use App\Models\LeadAlternativeAdd;
use App\Models\RaumGeometrie;
use App\Services\Anforderungsprofil\AnforderungsprofilService;
use App\Services\BuildingModel\CanonicalHash;
use App\Services\Geometrie\SzeneProjektionService;
use App\Services\Heizlast\GeometrieAbleitungService;

/**
 * P2-2 — Übernahme der Hausplaner-Szene in die belastbare Auslegung (gebaeude_geometrie).
 *
 * Grundlage: docs/planner-spec-p2-2-verdrahtung-szene-gebaeude-geometrie.md.
 *
 * Projiziert die Szene eines Objekts (SzeneProjektionService, Polygone TopologieGate-geprüft),
 * übersetzt jeden Raum in das gebaeude_geometrie-Format (GeometrieAbleitungService::ausGeometrie) und
 * schreibt das Ergebnis über den BESTEHENDEN Versionspfad (AnforderungsprofilService) als NEUE, aktive
 * Anforderungsprofil-Version am Objekt — exakt wie GrundrissController::schreibeGeometrieVersion, nur die
 * Quelle ist die Szene. Append-only (alte Version → abgelöst), kein destruktives Überschreiben, keine
 * raum_geometrien-Persistenz, keine Migration (gebaeude_geometrie-Spalte + Versionierung existieren).
 *
 * Idempotenz: gleicher Szenen-Quell-Hash wie die aktive Version ⇒ KEINE neue Version.
 * Ungültige Geometrie ⇒ GeometrieUngueltigException aus dem Gate (nichts geschrieben).
 * Der Nutzer-Auslöser (expliziter „Übernehmen"-Knopf) ist P2-2b und NICHT Teil dieser Action.
 */
class UebernehmeSzeneInAuslegung
{
    public function __construct(
        private SzeneProjektionService $projektion,
        private GeometrieAbleitungService $ableitung,
        private AnforderungsprofilService $profile,
    ) {}

    /**
     * @return array{status: string, raeume: int, version: int|null}
     */
    public function ausfuehren(LeadAlternativeAdd $objekt, ?int $userId): array
    {
        $dokument = HausplanerDocument::query()->where('alternative_id', $objekt->id)->first();
        if ($dokument === null) {
            return ['status' => 'keine_szene', 'raeume' => 0, 'version' => null];
        }

        $szene = $dokument->scene_json ?? [];

        // Wirft GeometrieUngueltigException bei ungültiger Geometrie ⇒ nichts wird geschrieben.
        $projiziert = $this->projektion->projiziere($szene);
        if ($projiziert === []) {
            return ['status' => 'kein_raum', 'raeume' => 0, 'version' => null];
        }

        $raeume = [];
        foreach ($projiziert as $r) {
            $geo = new RaumGeometrie([
                'polygon' => $r['polygon'],
                'hoehe_mm' => $r['hoehe_mm'],
                'wand_segmente' => $r['wand_segmente'],
                'decke' => $r['decke'],
                'boden' => $r['boden'],
            ]);
            $geo->setRelation('heizlastRaum', new HeizlastRaum(['name' => 'Raum', 'nutzung' => 'wohnen']));
            $raeume[] = $this->ableitung->ausGeometrie($geo);
        }

        $quellHash = CanonicalHash::of($szene);
        $gebaeudeGeometrie = [
            'raeume' => $raeume,
            'quelle' => 'hausplaner_szene',
            'quell_hash' => $quellHash,
        ];

        $aktiv = Anforderungsprofil::query()->aktiv()
            ->where('verankerbar_type', $objekt->getMorphClass())
            ->where('verankerbar_id', $objekt->getKey())
            ->first();

        // Idempotenz: dieselbe Szene bereits übernommen ⇒ keine neue Version (kein Versionsmüll).
        if ($aktiv !== null && is_array($aktiv->gebaeude_geometrie)
            && ($aktiv->gebaeude_geometrie['quell_hash'] ?? null) === $quellHash) {
            return ['status' => 'unveraendert', 'raeume' => count($raeume), 'version' => (int) $aktiv->version];
        }

        $neu = $aktiv !== null
            ? $this->profile->neueVersion($aktiv, [], $userId)
            : $this->profile->anlegen($objekt, 'Hausplaner-Geometrie', [], $userId);

        $neu->gebaeude_geometrie = $gebaeudeGeometrie;
        $neu->save();
        $aktiviert = $this->profile->aktivieren($neu)->fresh();

        return ['status' => 'uebernommen', 'raeume' => count($raeume), 'version' => (int) $aktiviert->version];
    }
}
