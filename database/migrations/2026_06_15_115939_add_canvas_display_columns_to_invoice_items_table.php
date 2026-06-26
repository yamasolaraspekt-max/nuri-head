<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            if (!Schema::hasColumn('invoice_items', 'print_hidden')) {
                $table->boolean('print_hidden')->default(false)->after('sort_order');
            }

            if (!Schema::hasColumn('invoice_items', 'group_title')) {
                $table->string('group_title')->nullable()->after('print_hidden');
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            if (Schema::hasColumn('invoice_items', 'group_title')) {
                $table->dropColumn('group_title');
            }

            if (Schema::hasColumn('invoice_items', 'print_hidden')) {
                $table->dropColumn('print_hidden');
            }
        });
    }
};
