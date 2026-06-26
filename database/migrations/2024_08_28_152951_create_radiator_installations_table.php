<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('radiator_installations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('customer_id')
                ->constrained('new_leads')
                ->cascadeOnDelete();

            $table->foreignId('alternative_id')
                ->nullable()
                ->constrained('lead_alternative_adds')
                ->nullOnDelete();

            $table->string('postcode')->nullable();
            $table->string('address_no')->nullable();

            $table->string('number')->nullable();
            $table->string('type')->nullable();
            $table->string('radiator_type')->nullable();
            $table->string('floor')->nullable();
            $table->string('room')->nullable();

            $table->decimal('room_size', 10, 2)->nullable();

            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->integer('depth')->nullable();

            $table->integer('niche_top')->nullable();
            $table->integer('niche_bottom')->nullable();
            $table->integer('niche_left')->nullable();
            $table->integer('niche_right')->nullable();

            $table->boolean('has_window_sill')->default(false);

            $table->string('supply_valve')->nullable();
            $table->boolean('supply_valve_presettable')->default(false);

            $table->string('return_valve')->nullable();
            $table->boolean('return_valve_present')->default(false);

            $table->string('design')->nullable();

            $table->boolean('renew_thermostat_head')->default(false);
            $table->boolean('has_socket')->default(false);

            $table->decimal('socket_distance', 10, 2)->nullable();

            $table->string('limbs')->nullable();
            $table->string('image')->nullable();

            $table->timestamps();

            $table->index(['customer_id', 'alternative_id']);
            $table->index(['customer_id', 'postcode']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('radiator_installations');
    }
};