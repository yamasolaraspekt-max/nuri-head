<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Z2-W0-7 · Permission-Item `Planner` (Y-6, Yama 21.08.2026).
 *
 * **Wozu:** die 61 `/planner/*`-Routen und die Planner-API brauchen ein Item, auf das sich ein
 * `permission:Planner,<aktion>` beziehen kann. Bisher gibt es keins — gemessen über
 * `user_roll_items`: Customer, Email, Employee, Finance, Inquiry, Organization, Partner, Problem,
 * Product, Users, Programmer, Administrator, Super, Invoice. **Kein `Planner`.**
 *
 * **Additiv und idempotent** nach dem Hausmuster aus `DemoCompanyMasterDataSeeder.php:134`
 * (`updateOrInsert(['item' => …])`). Es werden **keine** Rechte vergeben — nur das Item angelegt,
 * damit es referenzierbar ist. Wer welches Recht bekommt, ist eine andere Entscheidung.
 *
 * **Kein `down()`-Löschen:** ein Item, auf das inzwischen `user_rolls`-Zeilen zeigen könnten, wird
 * nicht zurückgenommen (Rückfall-/Archiv-Regel). Der Rückweg dieses Auftrags ist der Schalter,
 * nicht das Entfernen der Zeile.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('user_roll_items')) {
            return;
        }

        DB::table('user_roll_items')->updateOrInsert(
            ['item' => 'Planner'],
            ['created_at' => now(), 'updated_at' => now()],
        );
    }

    public function down(): void
    {
        // bewusst leer — siehe Kopf: additiv, kein Rückbau von Stammdaten.
    }
};
