<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('lead_product_lists')) {
            return;
        }

        Schema::table('lead_product_lists', function (Blueprint $table) {
            if (!Schema::hasColumn('lead_product_lists', 'accepted_offer_folder_id')) {
                $table->unsignedBigInteger('accepted_offer_folder_id')->nullable()->after('lead_stage_sub_stage_id');
            }

            if (!Schema::hasColumn('lead_product_lists', 'offer_acceptance_status')) {
                $table->string('offer_acceptance_status', 80)->nullable()->after('accepted_offer_folder_id');
            }

            if (!Schema::hasColumn('lead_product_lists', 'moved_without_offer_acceptance')) {
                $table->boolean('moved_without_offer_acceptance')->default(false)->after('offer_acceptance_status');
            }

            if (!Schema::hasColumn('lead_product_lists', 'moved_without_offer_acceptance_at')) {
                $table->timestamp('moved_without_offer_acceptance_at')->nullable()->after('moved_without_offer_acceptance');
            }

            if (!Schema::hasColumn('lead_product_lists', 'moved_without_offer_acceptance_by')) {
                $table->unsignedBigInteger('moved_without_offer_acceptance_by')->nullable()->after('moved_without_offer_acceptance_at');
            }

            if (!Schema::hasColumn('lead_product_lists', 'moved_without_offer_acceptance_reason')) {
                $table->text('moved_without_offer_acceptance_reason')->nullable()->after('moved_without_offer_acceptance_by');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('lead_product_lists')) {
            return;
        }

        Schema::table('lead_product_lists', function (Blueprint $table) {
            $columns = [
                'moved_without_offer_acceptance_reason',
                'moved_without_offer_acceptance_by',
                'moved_without_offer_acceptance_at',
                'moved_without_offer_acceptance',
                'offer_acceptance_status',
                'accepted_offer_folder_id',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('lead_product_lists', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
