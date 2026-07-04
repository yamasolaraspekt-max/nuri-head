<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Zubehör-Stammdaten (Ventile/Köpfe/Adapter/…). kvs_werte als JSON (Voreinstellstufe→kv).
 * Neutraler Herstellerschlüssel (hersteller + herst_artikelnr), analog supplier_article_map (W2).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accessories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accessory_category_id')->nullable()->constrained('accessory_categories')->nullOnDelete();
            $table->string('hersteller', 191);
            $table->string('herst_artikelnr', 191);
            $table->string('name');
            $table->string('typ')->nullable();
            $table->unsignedInteger('dn')->nullable();
            $table->json('kvs_werte')->nullable();
            $table->enum('kopf_anschluss_norm', ['M30x1_5', 'RA', 'RAV', 'RAVL', 'sonstige'])->nullable();
            $table->boolean('einrohr_tauglich')->default(false);
            $table->boolean('voreinstellbar')->default(false);
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('quelle')->nullable();
            $table->boolean('aktiv')->default(true);
            $table->timestamps();
            $table->unique(['hersteller', 'herst_artikelnr']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accessories');
    }
};
