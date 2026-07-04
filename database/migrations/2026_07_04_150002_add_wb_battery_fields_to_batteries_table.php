<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * wberechnung-Cut-over Stufe (i): 4 additive Batterie-Felder (Spannungsfenster + Lade-/Strom-Grenzen).
 * Konsumiert von InverterSizingService:403-430 (SizingBattery-Contract). Alle nullable, Einheit im Kommentar.
 * Hinweis: p_lade_max ist in kW (Interface-Einheit); der Kern rechnet ×1000 gegen die WR-Spalte
 * battery_max_charge_power_w [W]. Beide Einheiten bewusst 1:1 vom Contract übernommen.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('batteries', function (Blueprint $table) {
            $table->integer('min_voltage')->nullable()->comment('[V] Batterie-Fenster min (wb u_min_v)');
            $table->integer('max_voltage')->nullable()->comment('[V] Batterie-Fenster max (wb u_max_v)');
            $table->float('max_charge_power_kw')->nullable()->comment('[kW] max Ladeleistung (wb p_lade_max_kw; Kern ×1000 vs WR _w)');
            $table->float('max_current_a')->nullable()->comment('[A] max Strom (wb i_max_a)');
        });
    }

    public function down(): void
    {
        Schema::table('batteries', function (Blueprint $table) {
            $table->dropColumn(['min_voltage', 'max_voltage', 'max_charge_power_kw', 'max_current_a']);
        });
    }
};
