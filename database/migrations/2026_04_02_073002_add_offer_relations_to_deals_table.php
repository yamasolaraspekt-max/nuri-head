<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            // 1. Add the columns
            // We use constrained() to ensure database integrity
            $table->foreignId('offer_id')
                ->nullable()
                ->after('id')
                ->constrained('offers')
                ->onDelete('set null');

            $table->foreignId('offer_folder_id')
                ->nullable()
                ->after('offer_id')
                ->constrained('offer_folders')
                ->onDelete('set null');
                
            // 2. Optional: Add an index for performance if you search by these often
            $table->index(['offer_id', 'offer_folder_id']);
        });
    }

    public function down(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            // Drop foreign keys first, then the columns
            $table->dropForeign(['offer_id']);
            $table->dropForeign(['offer_folder_id']);
            $table->dropColumn(['offer_id', 'offer_folder_id']);
        });
    }
};