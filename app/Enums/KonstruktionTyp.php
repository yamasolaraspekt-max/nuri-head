<?php

namespace App\Enums;

/**
 * Bauteilart eines wiederverwendbaren Aufbaus. Das Mapping auf den Rsi/Rse-Bauteiltyp
 * des UWertService (DIN EN ISO 6946) erfolgt über bauteiltyp().
 */
enum KonstruktionTyp: string
{
    case Aussenwand = 'aussenwand';
    case Innenwand = 'innenwand';
    case Dach = 'dach';
    case Decke = 'decke';
    case Boden = 'boden';
    case FassadeWdvs = 'fassade_wdvs';

    /**
     * Rsi/Rse-Bauteiltyp für UWertService::ausSchichten() (Schlüssel: wand|dach|decke|boden).
     */
    public function bauteiltyp(): string
    {
        return match ($this) {
            self::Aussenwand, self::Innenwand, self::FassadeWdvs => 'wand',
            self::Dach => 'dach',
            self::Decke => 'decke',
            self::Boden => 'boden',
        };
    }
}
