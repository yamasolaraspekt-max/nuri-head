<?php

// database/migrations/2026_02_12_000001_create_position_qualifications_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('position_qualifications', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();              // e.g. Geselle
            $table->decimal('default_price', 12, 2)->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('status')->default('Published');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('position_qualifications');
    }
};
