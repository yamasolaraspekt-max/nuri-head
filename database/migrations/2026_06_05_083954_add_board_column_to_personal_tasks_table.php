<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('personal_tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('personal_tasks', 'board_column')) {
                $table->string('board_column', 40)->nullable()->after('due_time')->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('personal_tasks', function (Blueprint $table) {
            if (Schema::hasColumn('personal_tasks', 'board_column')) {
                $table->dropColumn('board_column');
            }
        });
    }
};
