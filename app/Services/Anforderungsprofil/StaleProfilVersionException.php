<?php

namespace App\Services\Anforderungsprofil;

use RuntimeException;

/**
 * Stufe 3b P0 (Auflage B) — optimistische Nebenläufigkeits-Ablehnung der Profilversionskette.
 *
 * Wird von AnforderungsprofilService::neueVersion geworfen, wenn die übergebene Basisversion nicht
 * (mehr) der aktuelle Kopf der Kette ist (inzwischen eine höhere Version existiert). Zielverhalten für
 * die LINEARE Kette (Yama/Planner-Entscheidung): ein zweiter Save auf einer veralteten Basis wird als
 * Konflikt abgelehnt — es wird NICHT stillschweigend eine Folgeversion aus alten Daten erzeugt (kein
 * automatisches Rebase/Branching ohne ausdrücklich freigegebene Regel).
 */
class StaleProfilVersionException extends RuntimeException
{
    public function __construct(
        public readonly int $basisVersion,
        public readonly int $aktuelleVersion,
    ) {
        parent::__construct(sprintf(
            'Basisversion %d ist veraltet (aktueller Kopf: %d). Neue Version abgelehnt (stale/conflict) — bitte auf dem aktuellen Stand neu aufsetzen.',
            $basisVersion,
            $aktuelleVersion,
        ));
    }
}
