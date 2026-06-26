<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('supplier_import_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('supplier_connection_id')
                ->nullable()
                ->constrained('supplier_connections')
                ->nullOnDelete();

            $table->string('status')->default('pending');
            $table->string('source_type')->nullable();

            $table->integer('total_items')->default(0);
            $table->integer('success_items')->default(0);
            $table->integer('failed_items')->default(0);

            $table->text('message')->nullable();
            $table->json('payload')->nullable();

            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_import_logs');
    }
};