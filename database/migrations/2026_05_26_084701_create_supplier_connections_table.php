<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('supplier_connections', function (Blueprint $table) {
            $table->id();

            $table->foreignId('distributor_id')
                ->nullable()
                ->constrained('distributors')
                ->nullOnDelete();

            $table->string('name');
            $table->string('supplier_key')->unique();

            $table->string('connector_type')->default('ids');
            // ids, oci, api, csv, xml, bmecat, datanorm

            $table->string('auth_type')->default('basic');
            // none, basic, token, bearer, custom

            $table->string('endpoint_url')->nullable();
            $table->string('test_url')->nullable();
            $table->string('return_url')->nullable();
 
            $table->text('username')->nullable();
            $table->text('password')->nullable();
            $table->text('customer_number')->nullable();
            $table->text('token')->nullable(); 
            $table->json('extra_auth_data')->nullable(); 
            $table->json('request_config')->nullable();
 
            $table->json('import_config')->nullable();

            $table->string('last_test_status')->nullable();
            // success, failed

            $table->text('last_test_message')->nullable();
            $table->timestamp('last_tested_at')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_connections');
    }
};