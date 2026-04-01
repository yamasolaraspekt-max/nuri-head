<?php
// database/migrations/2026_03_05_000002_create_costing_set_roles_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('costing_set_roles', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('costing_set_id');
            $table->unsignedBigInteger('qualification_id'); // position_qualifications.id

            // Cost build-up
            $table->decimal('wage_cost_per_hour', 10, 2)->default(0);        // direct wage
            $table->decimal('payroll_overhead_percent', 6, 2)->default(0);   // social/vacation/etc
            $table->decimal('company_overhead_percent', 6, 2)->default(0);   // office/tools/IT/etc

            // computed / stored (optional but fast)
            $table->decimal('full_cost_rate_per_hour', 10, 2)->default(0);   // wage*(1+...)
            $table->decimal('sell_rate_per_hour', 10, 2)->default(0);        // optional VK/h

            $table->unsignedInteger('sort_order')->default(0);
            $table->string('status')->default('active'); // active|archived

            $table->timestamps();

            $table->foreign('costing_set_id')->references('id')->on('costing_sets')->onDelete('cascade');
            $table->foreign('qualification_id')->references('id')->on('position_qualifications')->onDelete('cascade');

            $table->unique(['costing_set_id', 'qualification_id'], 'csr_unique_set_role');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('costing_set_roles');
    }
};