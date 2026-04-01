<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('personal_tasks', function (Blueprint $table) {
            $table->boolean('is_report')->default(false)->after('public');
            $table->timestamp('archived_at')->nullable()->after('is_notified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personal_tasks', function (Blueprint $table) {
            $table->boolean('is_report')->default(false)->after('public');
            $table->timestamp('archived_at')->nullable()->after('is_notified');
        });
    }
};
