<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('main_appointment_reminder_logs', function (Blueprint $table) {
            $table->unsignedTinyInteger('reminder_count')->default(0)->after('reminder_at');
            $table->timestamp('last_reminded_at')->nullable()->after('reminder_count');
        });
    }

    public function down(): void
    {
        Schema::table('main_appointment_reminder_logs', function (Blueprint $table) {
            $table->dropColumn(['reminder_count', 'last_reminded_at']);
        });
    }
};