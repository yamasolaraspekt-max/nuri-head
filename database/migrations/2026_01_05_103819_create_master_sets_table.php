<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('master_sets', function (Blueprint $table) {
            $table->id();

            // Dashboard category = your article_groups
            $table->unsignedBigInteger('article_group_id');

            $table->string('name')->nullable();
            $table->longText('description')->nullable();

            // Person responsible (ties position+department+employee together)
            $table->unsignedBigInteger('responsible_department_position_id')->nullable();

            $table->string('status')->default('Published');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('article_group_id')
                ->references('id')->on('article_groups')
                ->onDelete('cascade');

            $table->foreign('responsible_department_position_id')
                ->references('id')->on('department_positions')
                ->nullOnDelete();
 

             
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_sets');
    }
};
