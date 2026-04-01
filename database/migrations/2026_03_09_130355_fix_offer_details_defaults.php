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
        Schema::table('offer_details', function (Blueprint $table) {
        // Change columns to be nullable or have a default value of 0
            $table->decimal('total_net', 15, 2)->default(0)->change();
            $table->decimal('tax_rate', 5, 2)->default(19)->change();
            $table->decimal('total_gross', 15, 2)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
