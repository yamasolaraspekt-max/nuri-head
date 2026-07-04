<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Erweitert die BESTEHENDE Heizkörper-Bestandsaufnahme radiator_installations (W1) additiv:
 * Katalog-Link + Rechen-Input + Anschluss-/Ventil-Bestand + Heizkreis-Referenz.
 * Maße H/L/T bleiben in den vorhandenen Spalten width/height/depth (KEINE Duplikate).
 * ticket.radiators (Wechselrichter-Altlast) wird NICHT angefasst.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('radiator_installations', function (Blueprint $table) {
            $table->foreignId('radiator_spec_id')->nullable()->after('id')->constrained('product_radiator_specs')->nullOnDelete();
            $table->unsignedInteger('anzahl')->default(1);
            $table->enum('anschluss_position', ['seitlich', 'unten', 'mittel', 'wechselseitig'])->nullable();
            $table->enum('anschluss_fuehrung', ['zweirohr', 'einrohr'])->nullable();
            $table->string('ventil_einsatz_bestand')->nullable();
            $table->enum('kopf_norm_bestand', ['M30x1_5', 'RA', 'RAV', 'RAVL', 'sonstige'])->nullable();
            $table->foreignId('heating_circuit_id')->nullable()->constrained('heating_circuits')->nullOnDelete();
            $table->decimal('q_norm_w_pro_m_override', 8, 2)->nullable();
            $table->decimal('exponent_n_override', 4, 2)->nullable();
            $table->enum('typ_konfidenz', ['sicher', 'geschaetzt'])->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('radiator_installations', function (Blueprint $table) {
            $table->dropForeign(['radiator_spec_id']);
            $table->dropForeign(['heating_circuit_id']);
            $table->dropColumn([
                'radiator_spec_id',
                'anzahl',
                'anschluss_position',
                'anschluss_fuehrung',
                'ventil_einsatz_bestand',
                'kopf_norm_bestand',
                'heating_circuit_id',
                'q_norm_w_pro_m_override',
                'exponent_n_override',
                'typ_konfidenz',
            ]);
        });
    }
};
