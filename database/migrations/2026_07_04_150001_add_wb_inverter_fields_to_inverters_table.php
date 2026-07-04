<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * wberechnung-Cut-over Stufe (i): additive Wechselrichter-Felder + Strom-Typ-Angleichung.
 *
 * Konsumiert vom aktiven, DB-freien Kern App\Services\Energie\InverterSizingService (wberechnung),
 * angebunden in Stufe (iii) via Adapter/DTO (SizingInverter-Contract). Alle neuen Spalten NULLABLE,
 * Einheit im Kommentar (F3). 18 ADD (12 wb-Felder haben bereits ein ticket-Aequivalent -> Rename im
 * Adapter, nicht hier). F1: die 3 Strom-Spalten integer->float (Rundungsschutz; inverters = 0 Zeilen).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inverters', function (Blueprint $table) {
            // F1 — Ströme integer -> float (Nachkommastellen; sonst stiller Sizing-Rundungsfehler)
            $table->float('max_input_current')->nullable()->comment('[A] max DC-Eingangsstrom')->change();
            $table->float('max_input_current_per_mpp')->nullable()->comment('[A] max Strom je MPPT')->change();
            $table->float('max_short_circuit_current_per_mpp')->nullable()->comment('[A] max Kurzschlussstrom je MPPT')->change();

            // DC-Block (4)
            $table->integer('dc_operating_max_voltage')->nullable()->comment('[V] max Betriebs-DC-Spannung (wb u_dc_betrieb_max_v)');
            $table->integer('dc_startup_voltage')->nullable()->comment('[V] Einschalt-/Startspannung (wb u_dc_start_v)');
            $table->float('max_dc_ac_ratio')->nullable()->comment('[-] max DC/AC-Verhältnis (wb max_dc_ac_ratio)');
            $table->integer('max_array_power_wp')->nullable()->comment('[Wp] max Generatorleistung (wb max_array_wp)');

            // Hybrid / EPS (2)
            $table->boolean('is_hybrid')->nullable()->comment('[bool] Hybrid-WR (wb ist_hybrid)');
            $table->boolean('eps_capable')->nullable()->comment('[bool] EPS/Ersatzstrom-fähig (wb eps_faehig)');

            // Regulatorik / §14a (5)
            $table->boolean('integrated_grid_protection')->nullable()->comment('[bool] NA-Schutz integriert (wb na_schutz_integriert)');
            $table->boolean('vde4105_compliant')->nullable()->comment('[bool] VDE-AR-N 4105 konform (wb vde4105_konform)');
            $table->string('active_power_limit')->nullable()->comment('[enum] Wirkleistungsbegrenzung keine/60%/70% (wb wirkleistungsbegrenzung)');
            $table->boolean('controllable_14a')->nullable()->comment('[bool] steuerbar §14a EnWG (wb steuerbar_14a)');
            $table->string('control_interface')->nullable()->comment('[text] Steuer-Schnittstelle (wb schnittstelle)');

            // Temperatur (3)
            $table->integer('operating_temp_min_c')->nullable()->comment('[°C] Betriebstemperatur min (wb temp_betrieb_min_c)');
            $table->integer('operating_temp_max_c')->nullable()->comment('[°C] Betriebstemperatur max (wb temp_betrieb_max_c)');
            $table->integer('temp_derating_from_c')->nullable()->comment('[°C] Derating ab (wb temp_derating_ab_c)');

            // Batterie-Kopplung (4)
            $table->integer('battery_min_voltage')->nullable()->comment('[V] Batterie-Fenster min (wb u_bat_min_v)');
            $table->integer('battery_max_voltage')->nullable()->comment('[V] Batterie-Fenster max (wb u_bat_max_v)');
            $table->integer('battery_max_charge_power_w')->nullable()->comment('[W] max Batterie-Ladeleistung (wb p_bat_lade_max_w)');
            $table->float('battery_max_current_a')->nullable()->comment('[A] max Batteriestrom (wb i_bat_max_a)');
        });
    }

    public function down(): void
    {
        Schema::table('inverters', function (Blueprint $table) {
            $table->dropColumn([
                'dc_operating_max_voltage', 'dc_startup_voltage', 'max_dc_ac_ratio', 'max_array_power_wp',
                'is_hybrid', 'eps_capable',
                'integrated_grid_protection', 'vde4105_compliant', 'active_power_limit', 'controllable_14a', 'control_interface',
                'operating_temp_min_c', 'operating_temp_max_c', 'temp_derating_from_c',
                'battery_min_voltage', 'battery_max_voltage', 'battery_max_charge_power_w', 'battery_max_current_a',
            ]);

            // F1 zurück — float -> integer (exakter Vorzustand)
            $table->integer('max_input_current')->nullable()->change();
            $table->integer('max_input_current_per_mpp')->nullable()->change();
            $table->integer('max_short_circuit_current_per_mpp')->nullable()->change();
        });
    }
};
