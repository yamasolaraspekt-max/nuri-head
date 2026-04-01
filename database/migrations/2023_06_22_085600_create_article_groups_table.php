<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       if (!Schema::hasTable('article_groups')) {
            Schema::create('article_groups', function (Blueprint $table) {
                $table->id();
                $table->string('article_group');
                $table->string('initial')->nullable();
                $table->decimal('min_value', 10, 2)->nullable();
                $table->decimal('max_value', 10, 2)->nullable();
                $table->string('image')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_groups');
    }
};
