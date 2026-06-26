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
        Schema::create('new_leads', function (Blueprint $table) {
            $table->id();
            $table->string('customer_type');
            $table->string('customer_no')->nullable();
            $table->string('title')->nullable();
            $table->string('academic_title')->nullable();
            $table->string('firma')->nullable();
            $table->string('lastname')->nullable();
            $table->string('name')->nullable();
            $table->longText('full_address')->nullable();
            $table->string('street')->nullable();
            $table->decimal('latitude', 35, 30)->nullable();
            $table->decimal('longitude', 35, 30)->nullable(); 
            $table->float('polygon_height')->nullable();
            $table->float('polygon_width')->nullable();
            $table->float('polygon_area')->nullable();
            $table->float('elevation')->nullable();
            $table->string('postcode')->nullable();
            $table->string('city')->nullable();
            $table->string('phone')->nullable();
            $table->string('telephone')->nullable();
            $table->string('email')->nullable();
            $table->string('source')->nullable();  
            $table->unsignedBigInteger('contact_person')->nullable(); 
            $table->unsignedBigInteger('branch')->nullable(); 
            $table->integer('interest_rating')->default(0);
            $table->integer('seriousness_rating')->default(0);
            $table->integer('price_information')->default(0); 
            $table->string('status')->nullable(); 
            $table->string('status_msg')->nullable(); 
            $table->longtext('info')->nullable();
            $table->string('purchase_status')->nullable();
            $table->decimal('total_purchase',10,2)->default(0);
            $table->integer('default_project_minutes')->nullable();
            $table->date('purchase_date')->nullable(); 
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('contact_person')->references('id')->on('employees')->onDelete('cascade'); 
            $table->foreign('branch')->references('id')->on('branches')->onDelete('cascade'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('new_leads');
    }
};
 