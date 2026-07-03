<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * S1-01 — Sequenztabelle für die lückenarme Rechnungsnummernvergabe.
 * Eine Zeile je (type, year); `current_number` = zuletzt vergebene Nummer.
 * Bewusst eigenständig (nicht accounting_number_ranges), spätere Zusammenführung möglich.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_number_ranges', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20);            // z. B. 'invoice'
            $table->string('prefix', 10);          // z. B. 'RE-'
            $table->smallInteger('year')->unsigned();
            $table->unsignedBigInteger('current_number')->default(0);
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->unique(['type', 'year'], 'invoice_number_ranges_type_year_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_number_ranges');
    }
};
