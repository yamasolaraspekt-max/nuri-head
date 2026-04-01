<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('distributors', function (Blueprint $table) {
            $table->string('short_name')->nullable()->after('id');          // Kurzname
            $table->timestamp('created_date')->nullable()->after('short_name'); // Erstanlagedatum
            $table->string('type')->nullable()->after('created_date');      // Typ
            $table->string('salutation')->nullable()->after('type');        // Anrede
            $table->string('name')->nullable()->change();                   // already exists, allow nullable
            $table->string('name_suffix')->nullable()->after('name');       // Namenszusatz
            $table->string('street')->nullable()->after('name_suffix');     // Straße
            $table->string('postal_code')->nullable()->after('street');     // PLZ
            $table->string('city')->nullable()->after('postal_code');       // Ort
            $table->string('phone')->nullable()->after('city');             // Telefon
            $table->string('email')->nullable()->after('phone');            // Kommunikation
            $table->string('mobile')->nullable()->after('email');           // Mobiltelefon
            $table->string('account_number')->nullable()->after('mobile');  // Konto
            $table->boolean('is_hidden')->default(false)->after('account_number'); // Ausgeblendet
            $table->timestamp('last_changed_at')->nullable()->after('is_hidden');  // Änderungsdatum
            $table->string('editor')->nullable()->after('last_changed_at'); // Bearbeiter
            $table->string('owner')->nullable()->after('editor');           // Eigentümer
        });
    }

    public function down(): void
    {
        Schema::table('distributors', function (Blueprint $table) {
            $table->dropColumn([
                'short_name',
                'created_date',
                'type',
                'salutation',
                'name_suffix',
                'street',
                'postal_code',
                'city',
                'phone',
                'email',
                'mobile',
                'account_number',
                'is_hidden',
                'last_changed_at',
                'editor',
                'owner'
            ]);
        });
    }
};
