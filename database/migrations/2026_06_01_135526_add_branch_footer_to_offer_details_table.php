<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('offer_details', function (Blueprint $table) {
            if (!Schema::hasColumn('offer_details', 'branch_id')) {
                $table->unsignedBigInteger('branch_id')->nullable()->after('offer_folder_id');
            }

            if (!Schema::hasColumn('offer_details', 'company_footer')) {
                $table->json('company_footer')->nullable()->after('company_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('offer_details', function (Blueprint $table) {
            if (Schema::hasColumn('offer_details', 'company_footer')) {
                $table->dropColumn('company_footer');
            }

            if (Schema::hasColumn('offer_details', 'branch_id')) {
                $table->dropColumn('branch_id');
            }
        });
    }
};