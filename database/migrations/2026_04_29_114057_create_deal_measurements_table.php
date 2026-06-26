<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deal_measurements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('deal_id')->constrained('deals')->cascadeOnDelete();

            $table->unsignedBigInteger('offer_id')->nullable()->index();
            $table->unsignedBigInteger('offer_folder_id')->nullable()->index();
            $table->unsignedBigInteger('offer_detail_id')->nullable()->index();

            $table->unsignedBigInteger('customer_id')->nullable()->index();
            $table->unsignedBigInteger('alternative_id')->nullable()->index();
            $table->unsignedBigInteger('product_id')->nullable()->index();
            $table->unsignedBigInteger('department_id')->nullable()->index();

            $table->string('measurement_no')->nullable()->unique();
            $table->string('order_number')->nullable()->index();
            $table->string('offer_no')->nullable()->index();

            $table->string('status')->default('draft')->index();
            $table->timestamp('sent_at')->nullable();
            $table->unsignedBigInteger('sent_by')->nullable()->index();

            $table->json('sections_snapshot')->nullable();
            $table->json('material_summary')->nullable();

            $table->text('note')->nullable();

            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->unsignedBigInteger('updated_by')->nullable()->index();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['deal_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deal_measurements');
    }
};