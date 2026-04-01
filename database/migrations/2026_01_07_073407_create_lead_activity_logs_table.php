<?php
// database/migrations/xxxx_xx_xx_create_lead_activity_logs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lead_activity_logs', function (Blueprint $table) {
            $table->id();
            // Context IDs
            $table->unsignedBigInteger('new_leads_id')->nullable()->index();
            $table->unsignedBigInteger('alternative_id')->nullable()->index();
            $table->unsignedBigInteger('product_id')->nullable()->index();
            
            // Who did it
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name')->nullable(); // Snapshot of name in case user is deleted

            // What happened
            $table->string('event_type'); // 'created', 'updated', 'deleted'
            $table->string('model_type'); // App\Models\NewLeads
            $table->unsignedBigInteger('model_id'); 

            // The Changes (JSON is best for "From -> To")
            $table->json('changes')->nullable(); // Stores { field: { from: 'x', to: 'y' } }
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lead_activity_logs');
    }
};