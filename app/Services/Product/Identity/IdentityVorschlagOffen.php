<?php

namespace App\Services\Product\Identity;

/**
 * Ein Stufe-5-Treffer (VORSCHLAG, E1) unterbricht den automatischen Pfad:
 * kein Anlegen, keine Zuordnung — ein Mensch entscheidet.
 *
 * Trägt Identität + Match nach AUSSEN, damit der Pfad die Vorschlagszeile
 * NACH dem Transaktions-Rollback schreiben kann (sonst rollte der Vorschlag
 * mit zurück und wäre eine Verwerfung mit Extraschritten, §7).
 */
final class IdentityVorschlagOffen extends \RuntimeException
{
    public function __construct(
        public readonly ProductIdentity $identity,
        public readonly IdentityMatch $match,
    ) {
        parent::__construct(
            'Identität nicht eindeutig — Stufe-5-Vorschlag zur Prüfung (Artikel #'
            . ($match->product?->id ?? '?') . '): ' . $match->begruendung
        );
    }
}
