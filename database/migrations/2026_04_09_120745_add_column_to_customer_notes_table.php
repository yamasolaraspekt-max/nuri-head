<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_notes', function (Blueprint $table) {
            $table->json('history')->nullable()->after('type');
            $table->json('read_by')->nullable()->after('history');
            $table->timestamp('last_read_at')->nullable()->after('read_by');
        });
    }

    public function down(): void
    {
        Schema::table('customer_notes', function (Blueprint $table) {
            $table->dropColumn(['history', 'read_by', 'last_read_at']);
        });
    }
};