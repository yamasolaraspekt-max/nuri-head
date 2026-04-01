<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('customer_notes', function (Blueprint $table) {
            // Stores the unique ID of the product in the project list
            $table->unsignedBigInteger('lead_product_list_id')->nullable()->after('product_id');
            $table->index('lead_product_list_id');
        });
    }

    public function down()
    {
        Schema::table('customer_notes', function (Blueprint $table) {
            $table->dropColumn('lead_product_list_id');
        });
    }
};