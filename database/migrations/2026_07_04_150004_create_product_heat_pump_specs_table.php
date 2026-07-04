<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * wberechnung-Cut-over Stufe (i): Wärmepumpen-Spec-Katalog (komplett additiv — ticket.heat_pumps ist
 * WP-Auswahl je Lead, kein Spec-Katalog). Konsumiert von WpKennlinieService + WaermepumpenMatchService.
 * Die 3-Ebenen-Leistungskurve (W35/W45/W55) als JSON-Spalte (strukturiert, kein EAV). product_id-FK,
 * alle nullable, Einheit im Kommentar. hersteller/modell kommen aus products (nicht doppeln).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_heat_pump_specs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->nullable()->comment('FK products');

            // 3-Ebenen-Kennlinie: {"35":[[t_C,p_kW,cop],...], "45":[...], "55":[...]} (WpKennlinieService:76)
            $table->json('leistungskurve')->nullable()->comment('Leistungskurve W35/W45/W55 je [t_C,p_kW,cop]');

            $table->float('heizleistung_am7_w35_kw')->nullable()->comment('[kW] Heizleistung A-7/W35');
            $table->float('heizleistung_a2_w35_kw')->nullable()->comment('[kW] Heizleistung A2/W35');
            $table->float('heizleistung_a7_w35_kw')->nullable()->comment('[kW] Heizleistung A7/W35');
            $table->float('heizleistung_am7_w55_kw')->nullable()->comment('[kW] Heizleistung A-7/W55');

            $table->float('cop_am7_w35')->nullable()->comment('[-] COP A-7/W35');
            $table->float('cop_a2_w35')->nullable()->comment('[-] COP A2/W35');
            $table->float('cop_a7_w35')->nullable()->comment('[-] COP A7/W35');
            $table->float('cop_am7_w55')->nullable()->comment('[-] COP A-7/W55');

            $table->float('scop_35')->nullable()->comment('[-] SCOP W35');
            $table->float('scop_55')->nullable()->comment('[-] SCOP W55');

            $table->float('max_vorlauf_c')->nullable()->comment('[°C] max Vorlauftemperatur');
            $table->float('aussen_heizen_min_c')->nullable()->comment('[°C] min Außentemperatur Heizbetrieb');

            $table->float('modulation_min_kw')->nullable()->comment('[kW] Modulation min');
            $table->float('modulation_max_kw')->nullable()->comment('[kW] Modulation max');

            // Auswahl-/Match-Labels (WaermepumpenMatchService); hersteller/modell aus products
            $table->string('geraetetyp')->nullable()->comment('Gerätetyp (luft_wasser etc.)');
            $table->string('serie')->nullable()->comment('Baureihe');
            $table->string('kaeltemittel')->nullable()->comment('Kältemittel');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_heat_pump_specs');
    }
};
