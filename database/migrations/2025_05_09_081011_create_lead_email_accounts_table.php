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
        Schema::create('lead_email_accounts', function (Blueprint $table) {
            $table->id();
             $table->string('label'); // e.g., 'leads', 'inquiry'
            $table->string('email');
            $table->string('password'); // store encrypted if possible
            $table->string('host')->default('imap.solar-aspekt.de');
            $table->integer('port')->default(993);
            $table->string('encryption')->default('ssl');
            $table->string('status')->default('Published');
            $table->string('test')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_email_accounts');
    }
};
