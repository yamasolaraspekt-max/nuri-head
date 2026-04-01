<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('offer_details', function (Blueprint $table) {
            $table->string('document_status', 20)
                ->default('offer')
                ->after('offer_folder_id');

            $table->json('angebot_snapshot_sections')
                ->nullable()
                ->after('sections');

            $table->timestamp('angebot_snapshot_at')
                ->nullable()
                ->after('angebot_snapshot_sections');
        });
    }

    public function down(): void
    {
        Schema::table('offer_details', function (Blueprint $table) {
            $table->dropColumn([
                'document_status',
                'angebot_snapshot_sections',
                'angebot_snapshot_at',
            ]);
        });
    }
};