<?php

namespace App\Services\BuildingModel;

use RuntimeException;

/**
 * G1b-1 — Projektionskonflikt: unter demselben Idempotenzschlüssel
 * (source_profile_id, source_version, schema_version, projection_version) existiert bereits eine Version
 * mit ABWEICHENDEM Payload-Hash. Es wird keine bestehende Zeile überschrieben und kein Duplikat erzeugt;
 * der Konflikt wird stabil signalisiert.
 */
final class ProjectionConflictException extends RuntimeException
{
    public function __construct(
        public readonly string $idempotencyKey,
        public readonly string $existingPayloadHash,
        public readonly string $incomingPayloadHash,
    ) {
        parent::__construct(
            "Projektionskonflikt für Schlüssel '{$idempotencyKey}': bestehender Payload-Hash weicht vom neuen ab "
            ."(bestehend={$existingPayloadHash}, neu={$incomingPayloadHash})."
        );
    }
}
