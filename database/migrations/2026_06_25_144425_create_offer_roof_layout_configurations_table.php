<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('offer_roof_layout_configurations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('offer_id')->nullable()->constrained('offers')->nullOnDelete();
            $table->foreignId('offer_folder_id')->nullable()->constrained('offer_folders')->nullOnDelete();
            $table->foreignId('offer_detail_id')->nullable()->constrained('offer_details')->nullOnDelete();
            $table->foreignId('offer_template_id')->nullable()->constrained('offer_templates')->nullOnDelete();

            $table->boolean('enabled')->default(false)->index();
            $table->string('title')->default('BELEGUNG DER DACHFLÄCHE');
            $table->string('offer_number')->nullable();

            $table->string('system_power_kwp')->nullable();
            $table->unsignedInteger('module_count')->nullable();
            $table->unsignedInteger('module_power_wp')->nullable();

            $table->json('selected_roofs')->nullable();
            $table->boolean('show_all_icons')->default(true);

            $table->string('compass_image_path')->nullable();
            $table->text('note')->nullable();
            $table->string('footer_company')->nullable();
            $table->json('meta')->nullable();

            $table->unsignedBigInteger('created_by_employee_id')->nullable()->index();
            $table->unsignedBigInteger('updated_by_employee_id')->nullable()->index();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['offer_id', 'offer_folder_id']);
            $table->index(['offer_detail_id']);
            $table->index(['offer_template_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offer_roof_layout_configurations');
    }
};
