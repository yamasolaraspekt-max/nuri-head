<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('general_task_steps')) {
            return;
        }

        Schema::table('general_task_steps', function (Blueprint $table) {
            if (!Schema::hasColumn('general_task_steps', 'due_at')) {
                $table->dateTime('due_at')->nullable()->after('description')->index();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('general_task_steps')) {
            return;
        }

        Schema::table('general_task_steps', function (Blueprint $table) {
            if (Schema::hasColumn('general_task_steps', 'due_at')) {
                $table->dropColumn('due_at');
            }
        });
    }
};
