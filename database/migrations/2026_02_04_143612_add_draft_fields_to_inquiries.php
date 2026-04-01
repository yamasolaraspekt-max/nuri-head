<?php

// database/migrations/2026_02_04_000000_add_draft_fields_to_inquiries.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->boolean('is_draft')->default(true)->after('status');
            $table->timestamp('last_autosaved_at')->nullable()->after('is_draft');
            $table->uuid('draft_uuid')->nullable()->unique()->after('last_autosaved_at');
        });
    }

    public function down(): void {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->dropColumn(['is_draft','last_autosaved_at','draft_uuid']);
        });
    }
};
