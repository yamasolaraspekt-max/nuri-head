<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offer_details', function (Blueprint $table) {
            if (!Schema::hasColumn('offer_details', 'offer_folder_id')) {
                $table->foreignId('offer_folder_id')
                    ->nullable()
                    ->after('offer_id')
                    ->constrained('offer_folders')
                    ->nullOnDelete()
                    ->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('offer_details', function (Blueprint $table) {
            if (Schema::hasColumn('offer_details', 'offer_folder_id')) {
                $table->dropConstrainedForeignId('offer_folder_id');
            }
        });
    }
};