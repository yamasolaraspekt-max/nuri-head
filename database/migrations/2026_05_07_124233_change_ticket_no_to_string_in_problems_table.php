<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('problems', function (Blueprint $table) {
            $table->string('ticket_no', 50)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('problems', function (Blueprint $table) {
            $table->integer('ticket_no')->nullable()->change();
        });
    }
};