<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * FS-02 — Formulare v2: additive Disziplinierungs-Spalten an product_formulas.
 *
 * - schema_version (int, Default 1): steuert Renderer/Validierung, erlaubt sanfte v1→v2-Migration.
 * - imported_from (nullable): Herkunfts-Marker für playground-Vorlagen (Rückbau-Beweis, FS-06).
 *
 * Rein additiv (2 nullable/Default-Spalten) — kein Eingriff in Bestandszeilen (DAUERDIREKTIVE/G3).
 * Prod-Lauf ist Tag-X (Skript + Rückbau unten bereit).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_formulas', function (Blueprint $table) {
            if (! Schema::hasColumn('product_formulas', 'schema_version')) {
                $table->unsignedSmallInteger('schema_version')->default(1)->after('fields');
            }
            if (! Schema::hasColumn('product_formulas', 'imported_from')) {
                $table->string('imported_from')->nullable()->after('schema_version');
            }
        });
    }

    public function down(): void
    {
        Schema::table('product_formulas', function (Blueprint $table) {
            foreach (['schema_version', 'imported_from'] as $col) {
                if (Schema::hasColumn('product_formulas', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
