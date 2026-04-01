<?php
 use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::table('maintenance_protocols', function (Blueprint $table) {
      if (!Schema::hasColumn('maintenance_protocols', 'appointment_id')) {
        $table->unsignedBigInteger('appointment_id')->nullable()->after('status');
        $table->foreign('appointment_id')->references('id')->on('main_appointments')->nullOnDelete();
        $table->index(['appointment_id']);
      }
    });
  }

  public function down(): void
  {
    Schema::table('maintenance_protocols', function (Blueprint $table) {
      if (Schema::hasColumn('maintenance_protocols', 'appointment_id')) {
        $table->dropForeign(['appointment_id']);
        $table->dropColumn('appointment_id');
      }
    });
  }
};
