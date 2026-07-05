<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Spec-Standard M-A (Baustufe 2): Herkunft/Prüfstand zentral an products (Annahme „1 Produkt = 1 Spec-Satz";
 * bei künftiger 1:n-Spec wandert der Status mit). Additiv/nullable, nur ticket_testing; main = M5-Deploy-Paket.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('verifikations_status')->nullable()->after('imported_from')
                ->comment('datenblatt_verifiziert | importiert_ungeprueft');
            $table->date('verifikations_datum')->nullable()->after('verifikations_status');
            $table->string('datenblatt_referenz')->nullable()->after('verifikations_datum');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['verifikations_status', 'verifikations_datum', 'datenblatt_referenz']);
        });
    }
};
