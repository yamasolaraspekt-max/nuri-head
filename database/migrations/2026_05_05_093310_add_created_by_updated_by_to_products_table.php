<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('status');
            }

            if (!Schema::hasColumn('products', 'updated_by')) {
                $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'updated_by')) {
                $table->dropColumn('updated_by');
            }

            if (Schema::hasColumn('products', 'created_by')) {
                $table->dropColumn('created_by');
            }
        });
    }
};