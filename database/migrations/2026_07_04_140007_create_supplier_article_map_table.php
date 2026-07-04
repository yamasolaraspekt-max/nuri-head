<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Neutraler Lieferanten-Artikel-Map (W2): Schlüssel = hersteller + herst_artikelnr, je Kanal.
 * Kanäle als austauschbare Mapper hinter Interface (Stufe iii); erster Mapper IDS/Sonepar.
 * bild_url nur opportunistisch (E5). Docket an products bzw. accessories.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_article_map', function (Blueprint $table) {
            $table->id();
            $table->string('hersteller', 191);
            $table->string('herst_artikelnr', 191);
            $table->enum('supplier_channel', ['ids', 'omd', 'datanorm']);
            $table->foreignId('distributor_id')->nullable()->constrained('distributors')->nullOnDelete();
            $table->string('lieferanten_artikelnr')->nullable();
            $table->decimal('ek_preis', 12, 2)->nullable();
            $table->decimal('vk_preis', 12, 2)->nullable();
            $table->string('bild_url')->nullable();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->foreignId('accessory_id')->nullable()->constrained('accessories')->nullOnDelete();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
            $table->unique(['hersteller', 'herst_artikelnr', 'supplier_channel'], 'supplier_article_map_neutral_key');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_article_map');
    }
};
