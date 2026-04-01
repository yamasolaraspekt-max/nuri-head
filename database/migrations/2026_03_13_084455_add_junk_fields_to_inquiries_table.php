<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            if (!Schema::hasColumn('inquiries', 'junk_reason')) {
                $table->string('junk_reason')->nullable()->after('reason');
            }

            if (!Schema::hasColumn('inquiries', 'junk_note')) {
                $table->text('junk_note')->nullable()->after('junk_reason');
            }

            if (!Schema::hasColumn('inquiries', 'junk_marked_at')) {
                $table->timestamp('junk_marked_at')->nullable()->after('junk_note');
            }

            if (!Schema::hasColumn('inquiries', 'junk_marked_by')) {
                $table->unsignedBigInteger('junk_marked_by')->nullable()->after('junk_marked_at');
            }

            // only add FK if employees table is used with numeric ids here
            $table->foreign('junk_marked_by')
                ->references('id')
                ->on('employees')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            if (Schema::hasColumn('inquiries', 'junk_marked_by')) {
                $table->dropForeign(['junk_marked_by']);
            }

            $table->dropColumn([
                'junk_reason',
                'junk_note',
                'junk_marked_at',
                'junk_marked_by',
            ]);
        });
    }
};