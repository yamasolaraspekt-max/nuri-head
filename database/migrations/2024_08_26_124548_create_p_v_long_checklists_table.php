<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('p_v_long_checklists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->nullable();  
             $table->string('desired_size')->nullable();
            $table->string('pv_rafters')->nullable();
            $table->string('evu_max_size')->nullable();
            $table->text('note')->nullable();  
            $table->string('year_of_construction')->nullable();
            $table->string('number_of_modules')->nullable();
            $table->string('module_manufacturer')->nullable();
            $table->string('type_designation')->nullable();
            $table->string('kwp_size')->nullable();
            $table->string('inverter')->nullable();
            $table->string('system_conversion')->nullable();
            $table->string('damage_defect')->nullable();
            $table->string('complete_dismantling')->nullable();
            $table->string('insurance_damage')->nullable();
            $table->string('customer_keeps_modules')->nullable();
            $table->string('customer_keeps_inverter')->nullable();
            $table->string('mieterstrommodell')->nullable();
            $table->string('waermepumpe')->nullable();
            $table->string('echeck')->nullable();
            $table->string('anzahl_we')->nullable();
            $table->string('wallbox')->nullable();
            $table->string('zaehlerschrank')->nullable();
            $table->string('position_hak')->nullable();
            $table->string('abstand_wechselrichter')->nullable();
            $table->string('abstand_neuer_zaehlerschrank')->nullable();
            $table->string('cabinet_size')->nullable();
            $table->string('erdung')->nullable();
            $table->integer('zaehler_abmeldung')->nullable();
            $table->integer('anzahl_zaehl_plaetze')->nullable();
            $table->integer('fi_anzahl')->nullable();
            $table->string('na_schutz')->default(false);
            $table->string('rundsteuerempfaenger')->default(false);
            $table->string('zaehleradapterplatte')->default(false);
            $table->string('ac_ueberspannungsschutz')->default(false);
            $table->string('sls_schalter')->default(false);
            $table->string('apz_feld')->default(false);
            $table->string('trenn_relais')->default(false);
            $table->string('potentialausgleichsschiene')->default(false);
            $table->string('wlan')->default(false);
            $table->string('lan')->default(false);
            $table->string('steckdose')->default(false);
            $table->string('sonstiges')->default(false);
            $table->string('sonstiges_input')->nullable();
            $table->foreign('product_id')->references('id')->on('article_groups')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('p_v_long_checklists');
    }
};
