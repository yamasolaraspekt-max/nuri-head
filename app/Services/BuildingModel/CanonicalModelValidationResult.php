<?php

namespace App\Services\BuildingModel;

/**
 * Ergebnis der semantischen Validierung eines kanonischen Gebäudemodells (3D-1A).
 *
 * Reiner Wertträger: sammelt mehrere Fehler mit stabilem Code, JSON-Pointer-artigem Pfad, Meldung und
 * Schweregrad. Keine Nebenwirkungen, keine DB, keine personenbezogenen Daten in Meldungen.
 */
final class CanonicalModelValidationResult
{
    public const SEVERITY_ERROR = 'error';
    public const SEVERITY_WARNING = 'warning';

    /** @var array<int, array{code:string, path:string, message:string, severity:string}> */
    private array $issues = [];

    public function add(string $code, string $path, string $message, string $severity = self::SEVERITY_ERROR): void
    {
        $this->issues[] = ['code' => $code, 'path' => $path, 'message' => $message, 'severity' => $severity];
    }

    /** @return array<int, array{code:string, path:string, message:string, severity:string}> */
    public function issues(): array
    {
        return $this->issues;
    }

    /** @return array<int, array{code:string, path:string, message:string, severity:string}> */
    public function errors(): array
    {
        return array_values(array_filter($this->issues, fn ($i) => $i['severity'] === self::SEVERITY_ERROR));
    }

    /** @return list<string> alle Fehlercodes (Severity error), in Auftretensreihenfolge */
    public function errorCodes(): array
    {
        return array_map(fn ($i) => $i['code'], $this->errors());
    }

    public function hasErrorCode(string $code): bool
    {
        return in_array($code, $this->errorCodes(), true);
    }

    public function isValid(): bool
    {
        return $this->errors() === [];
    }
}
