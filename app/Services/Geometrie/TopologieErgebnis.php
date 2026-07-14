<?php

namespace App\Services\Geometrie;

/**
 * G0b / AP-4b — Ergebnis einer Topologie-/Plausibilitätsprüfung.
 * Unveränderlich. `valid` = keine Blocker; `blocker` = Liste je Regel mit rule_key + Klartext + Indizes.
 */
final class TopologieErgebnis
{
    /** @param array<int,array{rule_key:string,message:string,indices:array<int,int>}> $blocker */
    public function __construct(
        public readonly bool $valid,
        public readonly array $blocker = [],
    ) {}

    public static function ok(): self
    {
        return new self(true, []);
    }

    /** @param array<int,array{rule_key:string,message:string,indices:array<int,int>}> $blocker */
    public static function abgelehnt(array $blocker): self
    {
        return new self(false, $blocker);
    }

    /** @return array<int,string> */
    public function ruleKeys(): array
    {
        return array_values(array_map(fn ($b) => $b['rule_key'], $this->blocker));
    }
}
