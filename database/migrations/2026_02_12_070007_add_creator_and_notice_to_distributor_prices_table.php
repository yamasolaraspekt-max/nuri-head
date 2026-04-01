<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('distributor_prices', function (Blueprint $table) {
            $table->unsignedBigInteger('creator_id')->nullable()->after('product_id');
            $table->text('notice')->nullable()->after('availability');

            // optional FK if you have employees table:
            // $table->foreign('creator_id')->references('id')->on('employees')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('distributor_prices', function (Blueprint $table) {
            // optional: drop FK first if added
            // $table->dropForeign(['creator_id']);
            $table->dropColumn(['creator_id', 'notice']);
        });
    }
};
