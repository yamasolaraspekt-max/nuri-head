<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('master_set_tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('master_set_tasks', 'lead_stage_id')) {
                $table->foreignId('lead_stage_id')
                    ->nullable()
                    ->after('stage_id')
                    ->constrained('lead_stages')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('master_set_tasks', 'lead_sub_stage_id')) {
                $table->foreignId('lead_sub_stage_id')
                    ->nullable()
                    ->after('lead_stage_id')
                    ->constrained('lead_stage_sub_stages')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('master_set_tasks', function (Blueprint $table) {
            if (Schema::hasColumn('master_set_tasks', 'lead_sub_stage_id')) {
                $table->dropConstrainedForeignId('lead_sub_stage_id');
            }

            if (Schema::hasColumn('master_set_tasks', 'lead_stage_id')) {
                $table->dropConstrainedForeignId('lead_stage_id');
            }
        });
    }
};