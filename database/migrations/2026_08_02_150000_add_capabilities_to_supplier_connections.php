<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * AUF-IDS-LI-SV: Befund der IDS-Faehigkeitsabfragen (LI/SV) am Shop.
     * capabilities ist ein BEFUND (was der Shop uns sagt), request_config ist
     * KONFIGURATION (was wir dem Shop sagen) — bewusst getrennt, keine zweite Wahrheit.
     * Rein additiv, beide Spalten nullable.
     */
    public function up(): void
    {
        Schema::table('supplier_connections', function (Blueprint $table) {
            $table->json('capabilities')->nullable();
            $table->timestamp('capabilities_checked_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('supplier_connections', function (Blueprint $table) {
            $table->dropColumn(['capabilities', 'capabilities_checked_at']);
        });
    }
};
