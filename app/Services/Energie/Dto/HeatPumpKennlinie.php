<?php

declare(strict_types=1);

namespace App\Services\Energie\Dto;

use App\Services\Energie\Contracts\WpKennlinie;

/**
 * DTO/Adapter: mappt eine ticket-`product_heat_pump_specs`-Zeile (+ hersteller/modell aus
 * products/brands via Join) auf den WpKennlinie-Contract. Spaltennamen sind 1:1 identisch
 * mit den wb-Property-Namen. `leistungskurve` liegt in der DB als JSON-Text vor und wird zu
 * array|null dekodiert (ungültig/leer -> null).
 */
final class HeatPumpKennlinie implements WpKennlinie
{
    public ?array $leistungskurve;

    public ?string $kurve_semantik;

    public ?float $heizleistung_am7_w35_kw;

    public ?float $heizleistung_a2_w35_kw;

    public ?float $heizleistung_a7_w35_kw;

    public ?float $heizleistung_am7_w55_kw;

    public ?float $cop_am7_w35;

    public ?float $cop_a2_w35;

    public ?float $cop_a7_w35;

    public ?float $cop_am7_w55;

    public ?float $scop_35;

    public ?float $scop_55;

    public ?float $max_vorlauf_c;

    public ?float $aussen_heizen_min_c;

    public ?float $modulation_min_kw;

    public ?float $modulation_max_kw;

    public ?string $geraetetyp;

    public ?string $serie;

    public ?string $kaeltemittel;

    public ?string $hersteller;

    public ?string $modell;

    public static function fromRow(array|object $row): self
    {
        $r = (array) $row;
        $o = new self;

        $o->leistungskurve = self::decodeKurve($r['leistungskurve'] ?? null);
        $o->kurve_semantik = self::nStr($r['kurve_semantik'] ?? null);

        $o->heizleistung_am7_w35_kw = self::nFloat($r['heizleistung_am7_w35_kw'] ?? null);
        $o->heizleistung_a2_w35_kw = self::nFloat($r['heizleistung_a2_w35_kw'] ?? null);
        $o->heizleistung_a7_w35_kw = self::nFloat($r['heizleistung_a7_w35_kw'] ?? null);
        $o->heizleistung_am7_w55_kw = self::nFloat($r['heizleistung_am7_w55_kw'] ?? null);

        $o->cop_am7_w35 = self::nFloat($r['cop_am7_w35'] ?? null);
        $o->cop_a2_w35 = self::nFloat($r['cop_a2_w35'] ?? null);
        $o->cop_a7_w35 = self::nFloat($r['cop_a7_w35'] ?? null);
        $o->cop_am7_w55 = self::nFloat($r['cop_am7_w55'] ?? null);

        $o->scop_35 = self::nFloat($r['scop_35'] ?? null);
        $o->scop_55 = self::nFloat($r['scop_55'] ?? null);

        $o->max_vorlauf_c = self::nFloat($r['max_vorlauf_c'] ?? null);
        $o->aussen_heizen_min_c = self::nFloat($r['aussen_heizen_min_c'] ?? null);
        $o->modulation_min_kw = self::nFloat($r['modulation_min_kw'] ?? null);
        $o->modulation_max_kw = self::nFloat($r['modulation_max_kw'] ?? null);

        $o->geraetetyp = self::nStr($r['geraetetyp'] ?? null);
        $o->serie = self::nStr($r['serie'] ?? null);
        $o->kaeltemittel = self::nStr($r['kaeltemittel'] ?? null);

        // aus products/brands via Join (Repository liefert Aliasse hersteller/modell)
        $o->hersteller = self::nStr($r['hersteller'] ?? null);
        $o->modell = self::nStr($r['modell'] ?? null);

        return $o;
    }

    /**
     * Dekodiert die JSON-Leistungskurve zu array|null. Bereits dekodierte Arrays werden
     * durchgereicht; ungültiges/leeres JSON ergibt null.
     */
    private static function decodeKurve(mixed $v): ?array
    {
        if ($v === null) {
            return null;
        }
        if (is_array($v)) {
            return $v;
        }
        if (! is_string($v) || trim($v) === '') {
            return null;
        }

        $decoded = json_decode($v, true);

        return is_array($decoded) ? $decoded : null;
    }

    private static function nFloat(mixed $v): ?float
    {
        return $v === null ? null : (float) $v;
    }

    private static function nStr(mixed $v): ?string
    {
        return $v === null ? null : (string) $v;
    }
}
