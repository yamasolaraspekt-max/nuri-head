<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_activity_filters', function (Blueprint $table) {
            if (!Schema::hasColumn('user_activity_filters', 'is_muted')) {
                $table->boolean('is_muted')->default(false)->after('product_ids');
            }

            if (!Schema::hasColumn('user_activity_filters', 'notification_types')) {
                $table->json('notification_types')->nullable()->after('is_muted');
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_activity_filters', function (Blueprint $table) {
            if (Schema::hasColumn('user_activity_filters', 'notification_types')) {
                $table->dropColumn('notification_types');
            }

            if (Schema::hasColumn('user_activity_filters', 'is_muted')) {
                $table->dropColumn('is_muted');
            }
        });
    }
};