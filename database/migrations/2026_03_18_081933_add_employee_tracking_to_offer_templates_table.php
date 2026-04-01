<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offer_templates', function (Blueprint $table) {
            if (!Schema::hasColumn('offer_templates', 'is_favorite')) {
                $table->boolean('is_favorite')
                    ->default(false)
                    ->after('description');
            }

            if (!Schema::hasColumn('offer_templates', 'favorite_by_employee_id')) {
                $table->foreignId('favorite_by_employee_id')
                    ->nullable()
                    ->after('is_favorite')
                    ->constrained('employees')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('offer_templates', 'favorite_at')) {
                $table->timestamp('favorite_at')
                    ->nullable()
                    ->after('favorite_by_employee_id');
            }

            if (!Schema::hasColumn('offer_templates', 'stamp')) {
                $table->string('stamp')
                    ->nullable()
                    ->after('favorite_at');
            }

            if (!Schema::hasColumn('offer_templates', 'stamped_by_employee_id')) {
                $table->foreignId('stamped_by_employee_id')
                    ->nullable()
                    ->after('stamp')
                    ->constrained('employees')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('offer_templates', 'stamped_at')) {
                $table->timestamp('stamped_at')
                    ->nullable()
                    ->after('stamped_by_employee_id');
            }

            if (!Schema::hasColumn('offer_templates', 'usage_count')) {
                $table->unsignedBigInteger('usage_count')
                    ->default(0)
                    ->after('stamped_at');
            }

            if (!Schema::hasColumn('offer_templates', 'last_used_by_employee_id')) {
                $table->foreignId('last_used_by_employee_id')
                    ->nullable()
                    ->after('usage_count')
                    ->constrained('employees')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('offer_templates', 'last_used_at')) {
                $table->timestamp('last_used_at')
                    ->nullable()
                    ->after('last_used_by_employee_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('offer_templates', function (Blueprint $table) {
            $foreigns = [];
            $columns = [];

            if (Schema::hasColumn('offer_templates', 'favorite_by_employee_id')) {
                $foreigns[] = 'favorite_by_employee_id';
                $columns[] = 'favorite_by_employee_id';
            }

            if (Schema::hasColumn('offer_templates', 'stamped_by_employee_id')) {
                $foreigns[] = 'stamped_by_employee_id';
                $columns[] = 'stamped_by_employee_id';
            }

            if (Schema::hasColumn('offer_templates', 'last_used_by_employee_id')) {
                $foreigns[] = 'last_used_by_employee_id';
                $columns[] = 'last_used_by_employee_id';
            }

            foreach ($foreigns as $foreign) {
                try {
                    $table->dropConstrainedForeignId($foreign);
                } catch (\Throwable $e) {
                    try {
                        $table->dropForeign([$foreign]);
                        $table->dropColumn($foreign);
                    } catch (\Throwable $e) {
                    }
                }
            }

            foreach ([
                'is_favorite',
                'favorite_at',
                'stamp',
                'stamped_at',
                'usage_count',
                'last_used_at',
            ] as $col) {
                if (Schema::hasColumn('offer_templates', $col)) {
                    $columns[] = $col;
                }
            }

            $columns = array_values(array_unique($columns));

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};