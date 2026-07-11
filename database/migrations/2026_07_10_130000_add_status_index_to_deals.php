<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Arbeitsliste-Performance: additiver Index deals.status.
 *
 * Belegte Entscheidung (EXPLAIN auf realistischem Volumen, ~1500 deals, 'deal' = Minderheit):
 * Die Arbeitslisten-Query „Auftraege ohne Rechnung" filtert `WHERE status='deal' ORDER BY id DESC
 * LIMIT n`. Ohne Index waehlt der Optimizer einen PRIMARY-reverse-Scan + Filter — schnell nur,
 * solange 'deal'-Zeilen dicht bei den neuesten ids liegen. Fuer un-berechnete (oft AELTERE)
 * Auftraege degradiert das mit wachsender Tabelle. Der Sekundaerindex (status) fuehrt die PK
 * implizit mit -> `status='deal' ORDER BY id DESC` wird per reverse-Indexscan OHNE filesort
 * bedient und ist auf die 'deal'-Zeilen begrenzt (bounded). EXPLAIN bestaetigt die Nutzung
 * (Index lookup on deals using deals_status_index (status = 'deal') (reverse)); EXPLAIN ANALYZE
 * zeigt keine Verschlechterung.
 *
 * BEWUSST NICHT gesetzt (per EXPLAIN begruendet, keine nutzlosen Indizes):
 *  - invoices.due_date  : existiert bereits (invoices_due_date_index), von der overdue-Query genutzt.
 *  - invoices.deal_id   : existiert bereits (invoices_deal_id_index), von der whereNotExists-Subquery genutzt.
 *  - personal_tasks     : die followup-Query (WHERE type/source_type/task_status … ORDER BY id DESC
 *                         LIMIT n) wird per PRIMARY-reverse-Scan + Filter + LIMIT-Early-Termination
 *                         bedient (KEIN filesort, bounded, billig). Der (type,source_type,source_id)-
 *                         Index wird dabei NICHT gewaehlt, weil er fuer ORDER BY id DESC einen filesort
 *                         erzwaenge — per EXPLAIN geprueft (auch als Minderheit) -> kein neuer Index.
 *  - invoices.paid_amount: `whereColumn('paid_amount','<','total_amount')` ist ein Spalte-zu-Spalte-
 *                         Vergleich; ein Einzelindex beschleunigt das NICHT (EXPLAIN: nur Residual-
 *                         Filter, die overdue-Query laeuft ueber den due_date-Range-Scan) -> nicht gesetzt.
 *
 * Rein additiv (DAUERDIREKTIVE: keine Bestandsdaten beruehrt), online (ALGORITHM=INPLACE bei MySQL),
 * mit vollem down(). Idempotent gegen Doppel-Anlage.
 */
return new class extends Migration
{
    private const TABLE = 'deals';
    private const INDEX = 'deals_status_index';
    private const COLUMN = 'status';

    public function up(): void
    {
        if (!Schema::hasTable(self::TABLE) || !Schema::hasColumn(self::TABLE, self::COLUMN)) {
            return;
        }
        if ($this->indexExists()) {
            return;
        }

        Schema::table(self::TABLE, function (Blueprint $table): void {
            $table->index(self::COLUMN, self::INDEX);
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable(self::TABLE) || !$this->indexExists()) {
            return;
        }

        Schema::table(self::TABLE, function (Blueprint $table): void {
            $table->dropIndex(self::INDEX);
        });
    }

    private function indexExists(): bool
    {
        return collect(Schema::getIndexes(self::TABLE))
            ->contains(fn ($idx) => ($idx['name'] ?? null) === self::INDEX);
    }
};
