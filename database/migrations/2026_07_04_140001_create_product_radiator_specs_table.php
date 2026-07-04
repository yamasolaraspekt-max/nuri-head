<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Heizkörper-Katalog (EN-442, parametrisch). Owner: Heizkörper-Strang.
 * Enthält die 3 vom wberechnung-Heizkörper-Kern (HeizkoerperService::qReal) konsumierten Felder:
 *   q_norm_w_pro_m (→ q_norm_w = ·Baulänge·Anzahl), exponent_n, norm_bedingung.
 * Der Cut-over-Strang liest später AUS dieser Tabelle (kein eigenes product_radiator_specs).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_radiator_specs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('hersteller')->nullable();
            $table->string('typ')->nullable();
            $table->enum('bauart', ['kompakt', 'glieder', 'roehren', 'konvektor', 'geblaesekonvektor', 'bad'])->nullable();
            $table->unsignedInteger('bauhoehe_mm')->nullable();
            $table->unsignedInteger('bautiefe_mm')->nullable();
            $table->decimal('q_norm_w_pro_m', 8, 2)->nullable();      // Kern-Feld 1 (EN 442)
            $table->string('norm_bedingung')->default('75/65/20');    // Kern-Feld 2 (Δt_log,norm)
            $table->decimal('exponent_n', 4, 2)->default(1.30);       // Kern-Feld 3 (Heizkörperexponent)
            $table->string('quelle')->nullable();
            $table->string('imported_from')->nullable();              // Herkunfts-Marker (z.B. 'wberechnung_seeder')
            $table->boolean('aktiv')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_radiator_specs');
    }
};
