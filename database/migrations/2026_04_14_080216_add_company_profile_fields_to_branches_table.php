<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable()->after('id');
            $table->string('second_color')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('web')->nullable();
            $table->string('bank')->nullable();
            $table->string('iban')->nullable();
            $table->string('bic')->nullable();
            $table->string('register')->nullable();
            $table->string('tax')->nullable();
            $table->string('vat')->nullable();
            $table->string('gf')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('logo_url')->nullable();
        });
    }

    public function down()
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn([
                'slug', 'second_color', 'whatsapp', 'web', 'bank', 
                'iban', 'bic', 'register', 'tax', 'vat', 'gf', 
                'contact_person', 'logo_url'
            ]);
        });
    }
};