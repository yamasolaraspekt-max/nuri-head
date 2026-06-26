<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) rename old columns only if old names exist and new names do not exist
        if (Schema::hasColumn('delivery_notes', 'from') && !Schema::hasColumn('delivery_notes', 'delivered_from')) {
            Schema::table('delivery_notes', function (Blueprint $table) {
                $table->renameColumn('from', 'delivered_from');
            });
        }

        if (Schema::hasColumn('delivery_notes', 'to') && !Schema::hasColumn('delivery_notes', 'branch_id')) {
            Schema::table('delivery_notes', function (Blueprint $table) {
                $table->renameColumn('to', 'branch_id');
            });
        }

        // 2) add columns only if they do not already exist
        Schema::table('delivery_notes', function (Blueprint $table) {
            if (!Schema::hasColumn('delivery_notes', 'delivered_from')) {
                $table->string('delivered_from')->nullable()->after('delivery_note');
            }

            if (!Schema::hasColumn('delivery_notes', 'branch_id')) {
                $table->unsignedBigInteger('branch_id')->nullable()->after('delivered_from');
            }

            if (!Schema::hasColumn('delivery_notes', 'handover_by')) {
                $table->unsignedBigInteger('handover_by')->nullable()->after('branch_id');
            }

            if (!Schema::hasColumn('delivery_notes', 'handover_date')) {
                $table->date('handover_date')->nullable()->after('handover_by');
            }

            if (!Schema::hasColumn('delivery_notes', 'description')) {
                $table->text('description')->nullable()->after('handover_date');
            }

            if (!Schema::hasColumn('delivery_notes', 'status')) {
                $table->string('status')->default('Verfügbar')->after('description');
            }

            if (!Schema::hasColumn('delivery_notes', 'progress')) {
                $table->unsignedTinyInteger('progress')->default(0)->after('status');
            }

            if (!Schema::hasColumn('delivery_notes', 'level')) {
                $table->unsignedTinyInteger('level')->default(1)->after('progress');
            }

            if (!Schema::hasColumn('delivery_notes', 'linked')) {
                $table->string('linked')->nullable()->after('level');
            }

            if (!Schema::hasColumn('delivery_notes', 'linked_delivery_note_id')) {
                $table->unsignedBigInteger('linked_delivery_note_id')->nullable()->after('linked');
            }

            if (!Schema::hasColumn('delivery_notes', 'pdf')) {
                $table->string('pdf')->nullable()->after('linked_delivery_note_id');
            }

            if (!Schema::hasColumn('delivery_notes', 'image')) {
                $table->string('image')->nullable()->after('pdf');
            }
        });

        // 3) add foreign keys only if not already present
        $foreignKeys = collect(DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'delivery_notes'
              AND REFERENCED_TABLE_NAME IS NOT NULL
        "))->pluck('CONSTRAINT_NAME')->toArray();

        Schema::table('delivery_notes', function (Blueprint $table) use ($foreignKeys) {
            if (Schema::hasColumn('delivery_notes', 'branch_id') && !in_array('delivery_notes_branch_id_foreign', $foreignKeys)) {
                $table->foreign('branch_id')
                    ->references('id')
                    ->on('branches')
                    ->nullOnDelete();
            }

            if (Schema::hasColumn('delivery_notes', 'handover_by') && !in_array('delivery_notes_handover_by_foreign', $foreignKeys)) {
                $table->foreign('handover_by')
                    ->references('id')
                    ->on('employees')
                    ->nullOnDelete();
            }

            if (Schema::hasColumn('delivery_notes', 'linked_delivery_note_id') && !in_array('delivery_notes_linked_delivery_note_id_foreign', $foreignKeys)) {
                $table->foreign('linked_delivery_note_id')
                    ->references('id')
                    ->on('delivery_notes')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        $foreignKeys = collect(DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'delivery_notes'
              AND REFERENCED_TABLE_NAME IS NOT NULL
        "))->pluck('CONSTRAINT_NAME')->toArray();

        Schema::table('delivery_notes', function (Blueprint $table) use ($foreignKeys) {
            if (in_array('delivery_notes_branch_id_foreign', $foreignKeys)) {
                $table->dropForeign('delivery_notes_branch_id_foreign');
            }

            if (in_array('delivery_notes_handover_by_foreign', $foreignKeys)) {
                $table->dropForeign('delivery_notes_handover_by_foreign');
            }

            if (in_array('delivery_notes_linked_delivery_note_id_foreign', $foreignKeys)) {
                $table->dropForeign('delivery_notes_linked_delivery_note_id_foreign');
            }
        });
    }
};