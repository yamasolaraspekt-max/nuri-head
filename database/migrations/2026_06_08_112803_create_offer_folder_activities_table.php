<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Create offer_folder_activities only if the table does not already exist.
     *
     * This migration is safe for existing databases:
     * - If offer_folder_activities already exists, it does nothing.
     * - If it does not exist, it creates the table needed by OfferFolderActivity.
     */
    public function up(): void
    {
        if (Schema::hasTable('offer_folder_activities')) {
            return;
        }

        Schema::create('offer_folder_activities', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('offer_folder_id')->nullable()->index();
            $table->unsignedBigInteger('offer_id')->nullable()->index();
            $table->unsignedBigInteger('employee_id')->nullable()->index();

            $table->string('type', 100)->nullable()->index();
            $table->string('title', 255)->nullable();
            $table->text('message')->nullable();
            $table->json('meta')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['offer_id', 'created_at'], 'ofa_offer_created_idx');
            $table->index(['offer_folder_id', 'created_at'], 'ofa_folder_created_idx');

            $table->foreign('offer_id')
                ->references('id')
                ->on('offers')
                ->nullOnDelete();

            $table->foreign('offer_folder_id')
                ->references('id')
                ->on('offer_folders')
                ->nullOnDelete();

            $table->foreign('employee_id')
                ->references('id')
                ->on('employees')
                ->nullOnDelete();
        });
    }

    /**
     * Drop only if the table exists.
     *
     * If this migration was skipped because the table already existed,
     * rolling back would still drop it. For safety, this down() is intentionally
     * conservative. Uncomment the drop line only if you really want rollback
     * to remove this table.
     */
    public function down(): void
    {
        // Schema::dropIfExists('offer_folder_activities');
    }
};
