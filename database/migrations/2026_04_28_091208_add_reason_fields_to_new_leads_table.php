<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('new_leads', function (Blueprint $table) {
            $table->text('delete_reason')->nullable()->after('status_msg');
            $table->unsignedBigInteger('deleted_by')->nullable()->after('delete_reason');
            $table->timestamp('deleted_reason_at')->nullable()->after('deleted_by');

            $table->text('junk_reason')->nullable()->after('deleted_reason_at');
            $table->unsignedBigInteger('junked_by')->nullable()->after('junk_reason');
            $table->timestamp('junked_at')->nullable()->after('junked_by');

            $table->text('unjunk_reason')->nullable()->after('junked_at');
            $table->unsignedBigInteger('unjunked_by')->nullable()->after('unjunk_reason');
            $table->timestamp('unjunked_at')->nullable()->after('unjunked_by');
        });
    }

    public function down(): void
    {
        Schema::table('new_leads', function (Blueprint $table) {
            $table->dropColumn([
                'delete_reason',
                'deleted_by',
                'deleted_reason_at',
                'junk_reason',
                'junked_by',
                'junked_at',
                'unjunk_reason',
                'unjunked_by',
                'unjunked_at',
            ]);
        });
    }
};