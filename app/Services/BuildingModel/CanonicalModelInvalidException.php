<?php

namespace App\Services\BuildingModel;

use RuntimeException;

/**
 * G1b-1 — wird geworfen, wenn ein Payload den semantischen Vertrag nicht erfüllt. Es wird KEINE Zeile
 * geschrieben. Die vollständige Fehlersammlung bleibt über result() erhalten (keine stille Korrektur).
 */
final class CanonicalModelInvalidException extends RuntimeException
{
    public function __construct(private readonly CanonicalModelValidationResult $result)
    {
        parent::__construct('Kanonisches Modell ungültig: '.implode(', ', $result->errorCodes()));
    }

    public function result(): CanonicalModelValidationResult
    {
        return $this->result;
    }
}
