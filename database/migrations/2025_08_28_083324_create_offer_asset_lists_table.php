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
        Schema::create('offer_asset_lists', function (Blueprint $table) {
            $table->id();

            $table->foreignId('offer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('offer_folder_id')->constrained('offer_folders')->cascadeOnDelete();

            // MasterSet this tool belongs to
            $table->foreignId('master_set_id')->nullable()
                ->constrained('product_master_sets')->nullOnDelete();

            // Which asset/tool
            $table->foreignId('asset_id')->nullable()
                ->constrained('assets')->nullOnDelete();

            $table->string('name');            // display name of tool/asset
            $table->decimal('rate', 10, 2)->default(0); // €/Stk
            $table->integer('qty')->default(1);
            $table->decimal('sum_total', 12, 2)->default(0);

            $table->integer('sort_order')->default(0);

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offer_asset_lists');
    }
};
