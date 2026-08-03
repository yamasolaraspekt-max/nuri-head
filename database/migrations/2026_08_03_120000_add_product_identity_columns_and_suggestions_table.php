<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * AUF-P1-S4 · Identitätsleiter, Schema nach Spez 11-…md §7.
 *
 * Streng additiv: drei nullable-Spalten + vier Indizes auf products (bisher
 * existiert dort KEIN einziger Index), neue Tabelle product_identity_suggestions
 * für Stufe-5-Vorschläge (E1). KEIN unique — der kommt erst nach belegter
 * Dublettenfreiheit als eigener Vorgang (§7/§12). Keine Bestandsspalte angefasst.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $t) {
            $t->string('gtin_normalized', 14)->nullable()->after('ean');
            $t->string('identity_rung', 8)->nullable();        // welche Stufe hat zugeordnet
            $t->string('completeness_level', 4)->nullable();   // I / II / III

            $t->index('article_no',      'products_article_no_idx');
            $t->index('sku',             'products_sku_idx');
            $t->index('gtin_normalized', 'products_gtin_idx');
            $t->index(['brand_id', 'model'], 'products_brand_model_idx');
        });

        Schema::create('product_identity_suggestions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('product_id')->constrained();     // der vermutete Treffer
            $t->json('incoming');                           // die ProductIdentity, die kam
            $t->string('channel', 64);
            $t->string('reason', 255);
            $t->string('status', 16)->default('offen');     // offen | bestaetigt | verworfen
            $t->foreignId('decided_by')->nullable()->constrained('users');
            $t->timestamps();
            $t->index(['status', 'channel']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_identity_suggestions');

        Schema::table('products', function (Blueprint $t) {
            $t->dropIndex('products_article_no_idx');
            $t->dropIndex('products_sku_idx');
            $t->dropIndex('products_gtin_idx');
            $t->dropIndex('products_brand_model_idx');

            $t->dropColumn(['gtin_normalized', 'identity_rung', 'completeness_level']);
        });
    }
};
