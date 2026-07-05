<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * B2a-1 Referenz-Katalog: wiederverwendbarer Bauteilaufbau (referenziert materials), U-Wert über
 * Schichten (DIN EN ISO 6946). Kein kaufbares Produkt. Additiv, lokal-first.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('konstruktionen', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('typ')->comment('aussenwand|innenwand|dach|decke|boden|fassade_wdvs');
            $table->json('schichten')->comment('[{material_id:int, dicke_mm:int, lambda_override:float|null}]');
            $table->decimal('u_wert_berechnet', 6, 3)->nullable()->comment('[W/m²K] gecacht');
            $table->string('quelle')->nullable();
            $table->boolean('ist_vorlage')->default(false);
            $table->string('verifikations_status')->nullable()->comment('din_belegt | importiert_ungeprueft');
            $table->string('imported_from')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('konstruktionen');
    }
};
