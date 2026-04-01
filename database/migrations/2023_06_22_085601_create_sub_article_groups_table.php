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
       if (!Schema::hasTable('sub_article_groups')) {
            Schema::create('sub_article_groups', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('article_group_id');
                $table->foreign('article_group_id')
                    ->references('id')
                    ->on('article_groups')
                    ->onDelete('cascade');
                $table->string('sub_article');
                $table->string('initial')->nullable();
                $table->string('value')->nullable();
                $table->string('status')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_article_groups');
    }
};
