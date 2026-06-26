<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('position_qualification_hierarchies', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('performer_qualification_id');
            $table->unsignedBigInteger('required_qualification_id');

            $table->boolean('allowed')->default(true);

            $table->decimal('efficiency_factor', 8, 4)->default(1.0000);
            $table->decimal('cost_factor', 8, 4)->default(1.0000);

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('performer_qualification_id', 'pqh_performer_idx');
            $table->index('required_qualification_id', 'pqh_required_idx');

            $table->unique(
                ['performer_qualification_id', 'required_qualification_id'],
                'pqh_performer_required_unique'
            );

            $table->foreign('performer_qualification_id', 'pqh_performer_fk')
                ->references('id')
                ->on('position_qualifications')
                ->cascadeOnDelete();

            $table->foreign('required_qualification_id', 'pqh_required_fk')
                ->references('id')
                ->on('position_qualifications')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('position_qualification_hierarchies');
    }
};