<?php

namespace App\Services\BuildingModel;

/**
 * G1b-1 — Referenz auf die Quellgeometrie einer Projektion. Die kanonische Schreibwahrheit bleibt
 * `anforderungsprofile.gebaeude_geometrie`; dieses Wertobjekt bündelt die Quell-Identität (Objekt, Profil,
 * Version) und die Rohgeometrie, aus der der Quell-Hash bestimmt wird. Der Store INTERPRETIERT die
 * Rohgeometrie NICHT (kein Mapper, kein Erfinden fehlender Operanden) — sie dient nur der Herkunft/Idempotenz.
 */
final class SourceGeometryRef
{
    /**
     * @param  array<string,mixed>  $sourceGeometry  Rohgeometrie (gebaeude_geometrie) — nur für Hash/Herkunft
     */
    public function __construct(
        private readonly int $objectId,
        private readonly int $profileId,
        private readonly int $version,
        private readonly array $sourceGeometry,
        private readonly ?string $capturedAt = null,
    ) {}

    public function objectId(): int
    {
        return $this->objectId;
    }

    public function profileId(): int
    {
        return $this->profileId;
    }

    public function version(): int
    {
        return $this->version;
    }

    public function capturedAt(): ?string
    {
        return $this->capturedAt;
    }

    public const SOURCE_TYPE = 'gebaeude_geometrie';

    /** Deterministischer Hash der Quellgeometrie (schlüssel-normalisiert, kein Nebeneffekt). */
    public function sourceHash(): string
    {
        return CanonicalHash::of($this->sourceGeometry);
    }
}
