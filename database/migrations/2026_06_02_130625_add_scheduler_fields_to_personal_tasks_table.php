<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('personal_tasks', function (Blueprint $table) {
            $table->dateTime('next_reminder_at')->nullable()->after('reminder_time');
            $table->dateTime('last_reminded_at')->nullable()->after('next_reminder_at');
            $table->unsignedInteger('reminder_count')->default(0)->after('last_reminded_at');

            $table->dateTime('next_repeat_at')->nullable()->after('repeat');
            $table->dateTime('last_repeated_at')->nullable()->after('next_repeat_at');
            $table->foreignId('repeat_parent_id')->nullable()->after('last_repeated_at')->constrained('personal_tasks')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('personal_tasks', function (Blueprint $table) {
            $table->dropConstrainedForeignId('repeat_parent_id');
            $table->dropColumn([
                'next_reminder_at',
                'last_reminded_at',
                'reminder_count',
                'next_repeat_at',
                'last_repeated_at',
            ]);
        });
    }
};