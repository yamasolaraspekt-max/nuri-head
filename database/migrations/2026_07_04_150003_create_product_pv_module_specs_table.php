<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * wberechnung-Cut-over Stufe (i): PV-Modul-Spec-Katalog (komplett additiv — ticket hat keine
 * PV-Modul-Spec-Tabelle). 11 Felder, 1:1 der SizingModule-Contract (InverterSizingService:81-243).
 * Muster analog inverters/batteries: product_id-FK (unsignedBigInteger, nullable), alle Werte nullable,
 * Einheit im Kommentar. Feldnamen 1:1 vom Interface (trivialer Adapter).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_pv_module_specs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->nullable()->comment('FK products');

            $table->float('voc_v')->nullable()->comment('[V] Leerlaufspannung STC (voc_v)');
            $table->float('vmpp_v')->nullable()->comment('[V] MPP-Spannung STC (vmpp_v)');
            $table->float('isc_a')->nullable()->comment('[A] Kurzschlussstrom STC (isc_a)');
            $table->float('impp_a')->nullable()->comment('[A] MPP-Strom STC (impp_a)');
            $table->integer('pmpp_wp')->nullable()->comment('[Wp] Nennleistung STC (pmpp_wp)');
            $table->float('tk_voc_pct_k')->nullable()->comment('[%/K] Temp.-Koeffizient Voc (tk_voc_pct_k)');
            $table->float('tk_isc_pct_k')->nullable()->comment('[%/K] Temp.-Koeffizient Isc (tk_isc_pct_k)');
            $table->float('tk_pmpp_pct_k')->nullable()->comment('[%/K] Temp.-Koeffizient Pmpp (tk_pmpp_pct_k)');
            $table->float('tk_vmpp_pct_k')->nullable()->comment('[%/K] Temp.-Koeffizient Vmpp (tk_vmpp_pct_k)');
            $table->integer('u_sys_max_v')->nullable()->comment('[V] max Systemspannung (u_sys_max_v)');
            $table->float('sicherung_max_a')->nullable()->comment('[A] max Strangsicherung (sicherung_max_a)');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_pv_module_specs');
    }
};
