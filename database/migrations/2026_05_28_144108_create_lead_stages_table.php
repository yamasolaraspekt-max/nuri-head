<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_stages', function (Blueprint $table) {
            $table->id();

            // Stored inside lead_product_lists.status
            $table->string('key')->unique();

            // Display name in UI
            $table->string('name');

            $table->string('color')->nullable();
            $table->string('icon')->nullable();

            $table->unsignedInteger('sort_order')->default(0);

            // Standard stages cannot be hard-deleted if protected
            $table->boolean('is_default')->default(false);
            $table->boolean('is_protected')->default(false);

            // For archive/junk/system stages
            $table->boolean('is_closed')->default(false);

            // Hide from active Kanban if needed
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();
        });

        DB::table('lead_stages')->insert([
            [
                'key' => 'lead',
                'name' => 'Lead',
                'color' => '#74b2d4',
                'icon' => 'user-plus',
                'sort_order' => 10,
                'is_default' => true,
                'is_protected' => true,
                'is_closed' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'offer',
                'name' => 'Angebot',
                'color' => '#93c21c',
                'icon' => 'file-text',
                'sort_order' => 20,
                'is_default' => true,
                'is_protected' => true,
                'is_closed' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'follow_up',
                'name' => 'Nachfassen',
                'color' => '#f8ac00',
                'icon' => 'phone-call',
                'sort_order' => 30,
                'is_default' => true,
                'is_protected' => true,
                'is_closed' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'accepted',
                'name' => 'Annehmen',
                'color' => '#93c21c',
                'icon' => 'check-circle',
                'sort_order' => 40,
                'is_default' => true,
                'is_protected' => true,
                'is_closed' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'deal',
                'name' => 'Auftrag',
                'color' => '#74b2d4',
                'icon' => 'briefcase',
                'sort_order' => 50,
                'is_default' => true,
                'is_protected' => true,
                'is_closed' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'project',
                'name' => 'Montage',
                'color' => '#c0d8ea',
                'icon' => 'tool',
                'sort_order' => 60,
                'is_default' => true,
                'is_protected' => true,
                'is_closed' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'completed',
                'name' => 'Abschluss',
                'color' => '#93c21c',
                'icon' => 'flag',
                'sort_order' => 70,
                'is_default' => true,
                'is_protected' => true,
                'is_closed' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'archive',
                'name' => 'Archive',
                'color' => '#6b7280',
                'icon' => 'archive',
                'sort_order' => 80,
                'is_default' => true,
                'is_protected' => true,
                'is_closed' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'junk',
                'name' => 'Junk',
                'color' => '#e50656',
                'icon' => 'trash-2',
                'sort_order' => 90,
                'is_default' => true,
                'is_protected' => true,
                'is_closed' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_stages');
    }
};