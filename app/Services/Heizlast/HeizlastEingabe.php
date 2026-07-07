<?php

namespace App\Services\Heizlast;

/**
 * Eingabe-DTO der WP-Auslegung (für beide Methoden). Unveränderlich.
 */
final class HeizlastEingabe
{
    public function __construct(
        // Methode
        public string $methode = 'beide',            // verbrauch | heizlast | beide
        // A.1 Gebäude
        public int $baujahr = 1990,
        public float $beheizte_wohnflaeche_m2 = 150.0,
        public int $geschosse = 2,
        public string $gebaeudetyp = 'freistehend',
        public float $raumtemp_soll_c = 20.0,
        public ?string $standort_plz = null,
        // A.2 Hülle
        public ?string $mauerwerk = null,
        public string $fassadendaemmung = 'keine',
        public ?float $daemmstaerke_fassade_cm = null,
        public string $dachdaemmung = 'keine',
        public ?float $daemmstaerke_dach_cm = null,
        public bool $kellerdecke_gedaemmt = false,
        public string $fensterverglasung = '2fach_ws',
        public string $lueftung = 'fenster',
        // A.3 Heizung
        public string $heizsystem = 'heizkoerper',
        public ?float $vorlauf_c = null,
        public ?int $alter_heizung_jahre = null,
        public string $aktuelles_heizmedium = 'erdgas',
        // A.4 Verbrauch
        public ?float $verbrauch_menge = null,
        public string $verbrauch_einheit = 'kWh',
        public int $verbrauch_zeitraum_jahre = 1,
        public bool $enthaelt_warmwasser = true,
        public string $erzeuger_typ = 'niedertemperatur',
        // A.5 Warmwasser / Nutzer
        public int $personen_im_haushalt = 3,
        public bool $badewanne_vorhanden = false,
        public ?string $ww_komfort = null,
        public bool $ww_mit_wp = true,
        // A.6 Wärmepumpe
        public string $wp_typ = 'luft_wasser',
        public int $evu_sperrzeit_h = 0,
        public string $betriebsweise = 'monoenergetisch',
        public ?int $wp_id = null, // gewähltes Gerät (Override); null = Vorschlag
        // Annahmen / Optionen
        public ?int $b_vh = null,
        public ?float $strompreis = null,
        // Direkte Übergabe (methode = 'direkt'): Heizlast aus dem raumweisen Konfigurator
        public ?float $phi_hl_kw = null,
    ) {}

    /**
     * @param  array<string, mixed>  $d
     */
    public static function fromArray(array $d): self
    {
        $std = new self;

        return new self(
            methode: $d['methode'] ?? $std->methode,
            baujahr: (int) ($d['baujahr'] ?? $std->baujahr),
            beheizte_wohnflaeche_m2: (float) ($d['beheizte_wohnflaeche_m2'] ?? $std->beheizte_wohnflaeche_m2),
            geschosse: (int) ($d['geschosse'] ?? $std->geschosse),
            gebaeudetyp: $d['gebaeudetyp'] ?? $std->gebaeudetyp,
            raumtemp_soll_c: (float) ($d['raumtemp_soll_c'] ?? $std->raumtemp_soll_c),
            standort_plz: $d['standort_plz'] ?? null,
            mauerwerk: $d['mauerwerk'] ?? null,
            fassadendaemmung: $d['fassadendaemmung'] ?? $std->fassadendaemmung,
            daemmstaerke_fassade_cm: isset($d['daemmstaerke_fassade_cm']) ? (float) $d['daemmstaerke_fassade_cm'] : null,
            dachdaemmung: $d['dachdaemmung'] ?? $std->dachdaemmung,
            daemmstaerke_dach_cm: isset($d['daemmstaerke_dach_cm']) ? (float) $d['daemmstaerke_dach_cm'] : null,
            kellerdecke_gedaemmt: (bool) ($d['kellerdecke_gedaemmt'] ?? false),
            fensterverglasung: $d['fensterverglasung'] ?? $std->fensterverglasung,
            lueftung: $d['lueftung'] ?? $std->lueftung,
            heizsystem: $d['heizsystem'] ?? $std->heizsystem,
            vorlauf_c: isset($d['vorlauf_c']) ? (float) $d['vorlauf_c'] : null,
            alter_heizung_jahre: isset($d['alter_heizung_jahre']) ? (int) $d['alter_heizung_jahre'] : null,
            aktuelles_heizmedium: $d['aktuelles_heizmedium'] ?? $std->aktuelles_heizmedium,
            verbrauch_menge: isset($d['verbrauch_menge']) ? (float) $d['verbrauch_menge'] : null,
            verbrauch_einheit: $d['verbrauch_einheit'] ?? $std->verbrauch_einheit,
            verbrauch_zeitraum_jahre: (int) ($d['verbrauch_zeitraum_jahre'] ?? $std->verbrauch_zeitraum_jahre),
            enthaelt_warmwasser: (bool) ($d['enthaelt_warmwasser'] ?? true),
            erzeuger_typ: $d['erzeuger_typ'] ?? $std->erzeuger_typ,
            personen_im_haushalt: (int) ($d['personen_im_haushalt'] ?? $std->personen_im_haushalt),
            badewanne_vorhanden: (bool) ($d['badewanne_vorhanden'] ?? false),
            ww_komfort: $d['ww_komfort'] ?? null,
            ww_mit_wp: (bool) ($d['ww_mit_wp'] ?? true),
            wp_typ: $d['wp_typ'] ?? $std->wp_typ,
            evu_sperrzeit_h: (int) ($d['evu_sperrzeit_h'] ?? 0),
            betriebsweise: $d['betriebsweise'] ?? $std->betriebsweise,
            wp_id: isset($d['wp_id']) ? (int) $d['wp_id'] : null,
            b_vh: isset($d['b_vh']) ? (int) $d['b_vh'] : null,
            strompreis: isset($d['strompreis']) ? (float) $d['strompreis'] : null,
            phi_hl_kw: isset($d['phi_hl_kw']) ? (float) $d['phi_hl_kw'] : null,
        );
    }
}
