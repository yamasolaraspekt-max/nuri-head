<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Zubehör-Kategorien (Ventile, Köpfe, Adapter, Hahnblöcke, Rücklaufverschraubungen, Einsätze). Heizkörper-Strang. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accessory_categories', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accessory_categories');
    }
};
