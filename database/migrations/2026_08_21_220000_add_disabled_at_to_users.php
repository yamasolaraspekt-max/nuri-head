<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Z2-W0-9 · `users.disabled_at` — der echte Kontostatus.
 *
 * **Warum eine neue Spalte und nicht `is_active`:** `is_active` ist ein ONLINE-Flag. Es wird von
 * `LogUserLogin.php:26` auf 1 und von `LogUserLogout.php:16` auf 0 gesetzt — bei jedem An- und
 * Abmelden. Wer es als Kontostatus benutzt, sperrt einen Nutzer mit dem Abmelden aus und hebt die
 * Sperre mit dem Anmelden wieder auf. Genau das passierte: die Oberfläche versprach „Deactivated",
 * der nächste Login setzte das Flag zurück, und der Nutzer war wieder drin.
 *
 * **Additiv und ohne Bestandswirkung:** die Spalte ist `nullable` und startet für ALLE Nutzer auf
 * `NULL`. Die Migration sperrt niemanden aus (Kriterium D). `is_active` bleibt unangetastet — kein
 * Bestandsverhalten kippt.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users') && ! Schema::hasColumn('users', 'disabled_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('disabled_at')->nullable()->after('is_active');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'disabled_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('disabled_at');
            });
        }
    }
};
