<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Leads-Kanban Stufe A (additive Bruecke): FK-Stage-Bindung der Gewerk-Karte.
 *
 * lead_product_lists.lead_stage_id -> lead_stages. Nullable, nullOnDelete, indiziert
 * (FK-Index). Zusaetzlich zum bestehenden String-Feld `status` (Legacy-Bruecke bleibt
 * unveraendert Wahrheit fuer alle heutigen Leser). lead_stage_sub_stage_id bleibt
 * unangetastet. Kein Verhaltenswechsel: das Board rendert weiter aus `status`.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('lead_product_lists') && !Schema::hasColumn('lead_product_lists', 'lead_stage_id')) {
            Schema::table('lead_product_lists', function (Blueprint $table) {
                $table->unsignedBigInteger('lead_stage_id')->nullable()->after('lead_stage_sub_stage_id');
                $table->foreign('lead_stage_id')
                      ->references('id')->on('lead_stages')
                      ->nullOnDelete(); // FK legt zugleich den Index an
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('lead_product_lists') && Schema::hasColumn('lead_product_lists', 'lead_stage_id')) {
            Schema::table('lead_product_lists', function (Blueprint $table) {
                $table->dropForeign(['lead_stage_id']);
                $table->dropColumn('lead_stage_id');
            });
        }
    }
};
