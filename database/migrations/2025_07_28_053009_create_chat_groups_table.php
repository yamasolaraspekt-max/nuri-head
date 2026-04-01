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
       Schema::create('chat_groups', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->nullable();

            // 🏷️ Optional group name and avatar
            $table->string('name')->nullable();
            $table->string('avatar')->nullable(); // You can store avatar path here

            // 👤 Group creator (admin)
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');

            $table->foreignId('customer_id')->nullable()->constrained('new_leads')->onDelete('set null');
            $table->foreignId('alternative_id')->nullable()->constrained('lead_alternative_adds')->onDelete('set null');
            $table->foreignId('product_id')->nullable()->constrained('article_groups')->onDelete('set null');
            $table->foreignId('lead_product_list_id')->nullable()->constrained('lead_product_lists')->nullOnDelete();

            // 📆 Timestamps
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_groups');
    }
};
