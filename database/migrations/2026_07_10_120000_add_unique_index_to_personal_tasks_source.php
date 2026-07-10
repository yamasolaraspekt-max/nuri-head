<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * A1b — Task-Race schließen: DB-Unique-Index als WAHRHEIT auf dem natürlichen Schlüssel
 * personal_tasks(type, source_type, source_id). Damit erzeugen zwei nebenläufige
 * FollowUpCreator::sync()-Aufrufe (Doppel-Submit/Queue-Retry) niemals zwei Zeilen —
 * der zweite Insert prallt am Constraint ab; der App-Catch lädt neu und aktualisiert.
 *
 * Additiv (DAUERDIREKTIVE): nur neuer Index, keine Bestandszeile geändert/gelöscht.
 *
 * NULL-Semantik bewusst: MySQL wertet NULL im Unique-Index nicht als gleich → manuelle
 * Tasks (source_type/source_id NULL) koexistieren weiter, der Index blockt sie nicht.
 *
 * Index-Längen-Befund (verifiziert, MySQL 9.7 / InnoDB DYNAMIC / utf8mb4, Limit 3072 Byte):
 *   type varchar(255) = 1020 B + source_type varchar(40) = 160 B + source_id bigint = 8 B
 *   = 1188 B < 3072 B → voller Unique-Index passt (kein Prefix, keine Verkürzung nötig).
 */
return new class extends Migration
{
    private const INDEX = 'personal_tasks_type_source_unique';

    public function up(): void
    {
        // PFLICHT-SICHERUNG: niemals still Daten zerstören. Existieren Dubletten auf dem
        // natürlichen Schlüssel, bricht die Migration ab und erzwingt die Reihenfolge.
        $dupes = DB::table('personal_tasks')
            ->selectRaw('COUNT(*) AS c')
            ->whereNotNull('source_type')
            ->whereNotNull('source_id')
            ->whereNull('deleted_at')
            ->groupBy('type', 'source_type', 'source_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        if ($dupes->isNotEmpty()) {
            throw new RuntimeException(
                'Dubletten auf personal_tasks(type, source_type, source_id) vorhanden ('
                . $dupes->count() . ' Schlüssel) — erst `php artisan followup:dedupe-tasks` laufen lassen, '
                . 'dann diese Migration erneut ausführen.'
            );
        }

        // Idempotenz: Index nur anlegen, wenn er noch nicht existiert.
        if ($this->indexExists(self::INDEX)) {
            return;
        }

        Schema::table('personal_tasks', function (Blueprint $table) {
            $table->unique(['type', 'source_type', 'source_id'], self::INDEX);
        });
    }

    public function down(): void
    {
        if (! $this->indexExists(self::INDEX)) {
            return;
        }

        Schema::table('personal_tasks', function (Blueprint $table) {
            $table->dropUnique(self::INDEX);
        });
    }

    private function indexExists(string $name): bool
    {
        return DB::table('information_schema.statistics')
            ->where('table_schema', DB::getDatabaseName())
            ->where('table_name', 'personal_tasks')
            ->where('index_name', $name)
            ->exists();
    }
};
