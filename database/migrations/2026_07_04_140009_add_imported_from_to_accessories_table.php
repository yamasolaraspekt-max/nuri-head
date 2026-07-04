<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Herkunfts-Marker für Zubehör-Stammdaten (analog product_radiator_specs.imported_from):
 * macht recherchierte/importierte Zeilen rückverfolgbar + restlos rückbaubar. Additiv, nullable.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accessories', function (Blueprint $table) {
            $table->string('imported_from')->nullable()->after('quelle');
        });
    }

    public function down(): void
    {
        Schema::table('accessories', function (Blueprint $table) {
            $table->dropColumn('imported_from');
        });
    }
};
