<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('sku')->nullable()->after('article_no');
            $table->string('heatpump_type')->nullable()->after('category');       // e.g. "Luft-Wasser"
            $table->string('construction_type')->nullable()->after('heatpump_type'); // e.g. "Monoblock"
            $table->string('refrigerant')->nullable()->after('construction_type'); // e.g. "R32", "R290"
            $table->tinyInteger('phase_count')->nullable()->after('refrigerant');  // e.g. 1 or 3
            $table->float('scop', 3, 2)->nullable()->after('phase_count');         // e.g. 4.6
            $table->float('noise_level_db', 4, 1)->nullable()->after('scop');      // e.g. 49.5
            $table->float('vat_percent', 4, 1)->nullable()->default(19.0)->after('noise_level_db'); // aka MwSt
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'sku',
                'heatpump_type',
                'construction_type',
                'refrigerant',
                'phase_count',
                'scop',
                'noise_level_db',
                'vat_percent',
            ]);
        });
    }
};
