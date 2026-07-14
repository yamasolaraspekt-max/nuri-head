<?php

namespace App\Services\Geometrie;

use RuntimeException;

/**
 * G0b / AP-4b — fachliche Ausnahme: ungültige Geometrie darf NICHT gerechnet werden.
 * Trägt das TopologieErgebnis (Blocker-Liste) für die Nutzerrückmeldung. Keine stille Reparatur.
 */
final class GeometrieUngueltigException extends RuntimeException
{
    public function __construct(public readonly TopologieErgebnis $ergebnis)
    {
        $keys = implode(', ', $ergebnis->ruleKeys());
        parent::__construct('Ungültige Geometrie abgelehnt (Topologie-Gate): '.$keys);
    }
}
