<?php

namespace App\Services\Suppliers\Ids;

/**
 * Befund der IDS-Faehigkeitsabfragen LI (§5.6) und SV (§5.7), IDS 2.5.
 * null bzw. leere Liste heisst: der Shop hat nicht (auswertbar) geantwortet.
 */
final class IdsCapabilities
{
    public function __construct(
        public readonly ?bool $kundennummerErforderlich,
        public readonly ?bool $benutzernameErforderlich,
        public readonly ?bool $passwortErforderlich,
        /** @var string[] z. B. ['1.3','2.0','2.5']; leer = unbekannt */
        public readonly array $versionen,
        /** @var string[] Versionen ausserhalb der sechs erlaubten Werte (Kante 8) */
        public readonly array $versionenUnbekannt,
        public readonly bool $liUnterstuetzt,
        public readonly bool $svUnterstuetzt,
        public readonly ?string $hinweis,
    ) {
    }

    public static function leer(?string $hinweis = null): self
    {
        return new self(null, null, null, [], [], false, false, $hinweis);
    }

    public function toArray(): array
    {
        return [
            'li_unterstuetzt' => $this->liUnterstuetzt,
            'sv_unterstuetzt' => $this->svUnterstuetzt,
            'kundennummer_erforderlich' => $this->kundennummerErforderlich,
            'benutzername_erforderlich' => $this->benutzernameErforderlich,
            'passwort_erforderlich' => $this->passwortErforderlich,
            'versionen' => $this->versionen,
            'versionen_unbekannt' => $this->versionenUnbekannt,
            'hinweis' => $this->hinweis,
        ];
    }
}
