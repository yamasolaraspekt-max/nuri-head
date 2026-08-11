<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\AuditableLead;
use App\Exceptions\RoofAzimuthOutOfRangeException;

class PVRoof extends Model
{
    use HasFactory;
    use AuditableLead;

    protected $fillable = [
        'customer_id',
        'alternative_id',

        'designation',
        'type',
        'roof_type',
        'roof',
        'roof_pitch',
        'roof_orientation',
        'roof_azimuth',
        'roof_area',
        'roof_height',
        'roof_age',

        'roof_covering',
        'roof_covering_name',
        'roof_covering_company',
        'roof_covering_model',
        'roof_covering_dimensions_cm',

        'roof_insulation',
        'thickness_roof_insulation',
        'between_rafter_insulation',
        'thickness_between_rafter',
        'insulation_material',

        'asbestos',
        'roof_renovation',
        'structural_analysis_available',
        'rafter_overhang_left',
        'rafter_overhang_right',
        'rafter_thickness',
        'rafter_reinforcement_needed',
        'scaffold_usage',
        'roof_structures',
        'roofer',

        'intention',
        'objective',
        'notes',
        'delivered_by',

        'number_we',
        'number_of_meters',
        'electricity_consumption',
        'construction_fluid',

        'electric_car',
        'number_of_electric_cars',
        'wallbox_desired',
        'number_of_wallboxes',

        'meter_cabinet',
        'meter_cabinet_company',
        'cabinet_size',
        'cabinet_size_sonstiges',

        'solar_holding_tile_desired',
        'kwp_size',
        'pv_existing',
        'construction_year',
        'module_count',
        'module_power',

        // extended wizard / migration fields
        'shading',
        'dc_cable_route',
        'storage_preference',
        'backup_power',
        'pv_investment_costs',
    ];

    protected $casts = [
        'asbestos' => 'boolean',
        'roof_renovation' => 'boolean',
        'structural_analysis_available' => 'boolean',
        'rafter_reinforcement_needed' => 'boolean',
        'scaffold_usage' => 'boolean',
        'roofer' => 'boolean',
        'electric_car' => 'boolean',
        'wallbox_desired' => 'boolean',
        'solar_holding_tile_desired' => 'boolean',
        'pv_existing' => 'boolean',

        'thickness_roof_insulation' => 'decimal:2',
        'thickness_between_rafter' => 'decimal:2',
        'roof_area' => 'decimal:2',
        'roof_age' => 'integer',
        'rafter_overhang_left' => 'decimal:2',
        'rafter_overhang_right' => 'decimal:2',
        'rafter_thickness' => 'decimal:2',
        'number_of_meters' => 'decimal:2',
        'electricity_consumption' => 'decimal:2',
        'number_of_electric_cars' => 'integer',
        'number_of_wallboxes' => 'integer',
        'kwp_size' => 'decimal:2',
        'module_count' => 'integer',
        'module_power' => 'decimal:2',
        'roof_pitch' => 'decimal:2',
        'roof_azimuth' => 'decimal:2',
        'roof_height' => 'decimal:2',
        'pv_investment_costs' => 'decimal:2',
    ];

    /**
     * Vertrag fuer `roof_azimuth` — Kompass-Konvention des Hauses: **0=N, 90=E, 180=S, 270=W**.
     *
     * Gueltig ist `0 <= x < 360`. Dieselbe Grenze wie `north_angle_deg` im kanonischen
     * Gebaeudemodell (dort als `minimum: 0` / `exclusiveMaximum: 360` zugesagt); `360` ist
     * derselbe Punkt wie `0`.
     *
     * **Die PVGIS-Konvention ist eine ANDERE** (dort ist `0` = Sued, Bereich -180..180). Sie ist
     * kein Fehler, sondern die Konvention einer Fremd-API — aber ein Wert aus diesem Feld darf
     * NICHT unveraendert an PVGIS gegeben werden. Der gefaehrliche Bereich ist `0..180`: er ist in
     * beiden Systemen gueltig und bedeutet das Gegenteil. Siehe **F-028** in
     * `docs/rollenkette/werkbank/01-MATHEMATIK/FORMELSAMMLUNG.md` — dort steht die Umrechnung,
     * hier steht sie bewusst NICHT (zwei Fassungen derselben Warnung waeren eine zweite Wahrheit).
     */
    public const AZIMUT_MIN = 0;

    /** Obergrenze, AUSSCHLIESSLICH — `360` ist ungueltig, siehe `AZIMUT_MIN`. */
    public const AZIMUT_MAX_EXKLUSIV = 360;

    /**
     * Prueft einen Azimutwert gegen den Vertrag. `null` und `''` sind gueltig (Feld ist nullable).
     *
     * Oeffentlich und statisch, damit die Zusage geprueft werden kann, ohne die Datenbank
     * anzufassen — und damit der Waechter unten genau dieselbe Frage stellt wie der Test.
     */
    public static function pruefeAzimut(mixed $wert): void
    {
        if ($wert === null || $wert === '') {
            return;
        }

        if (! is_numeric($wert) || $wert < self::AZIMUT_MIN || $wert >= self::AZIMUT_MAX_EXKLUSIV) {
            throw new RoofAzimuthOutOfRangeException($wert);
        }
    }

    /**
     * Der Waechter sitzt am Model, nicht im Controller: `saving` greift bei `create`, `update`
     * UND beim Mass-Assignment-Pfad `PVRoof::create($array)`. Ein Controller-Riegel wuerde jeden
     * anderen Schreibweg offen lassen.
     */
    protected static function booted(): void
    {
        static::saving(function (self $roof): void {
            self::pruefeAzimut($roof->roof_azimuth);
        });
    }

    public function customer()
    {
        return $this->belongsTo(NewLeads::class, 'customer_id');
    }

    public function alternative()
    {
        return $this->belongsTo(LeadAlternativeAdd::class, 'alternative_id');
    }

    public function products()
    {
        return $this->belongsTo(Products::class, 'roof_covering');
    }

    public function roofCoveringProduct()
    {
        return $this->belongsTo(Product::class, 'roof_covering');
    }
}