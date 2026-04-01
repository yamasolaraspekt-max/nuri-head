<?php

// database/migrations/2026_03_05_114700_add_default_active_to_costing_sets.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::table('costing_sets', function (Blueprint $table) {
      if (!Schema::hasColumn('costing_sets','is_default')) {
        $table->boolean('is_default')->default(false)->after('name');
      }
      if (!Schema::hasColumn('costing_sets','is_active')) {
        $table->boolean('is_active')->default(true)->after('is_default');
      }
    });
  }

  public function down(): void
  {
    Schema::table('costing_sets', function (Blueprint $table) {
      if (Schema::hasColumn('costing_sets','is_default')) $table->dropColumn('is_default');
      if (Schema::hasColumn('costing_sets','is_active'))  $table->dropColumn('is_active');
    });
  }
};