<?php

namespace Tests\Feature\FollowUp;

use App\Models\PersonalTask;
use App\Services\FollowUp\FollowUpCreator;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * A1b — Task-Race schließen: DB-Unique-Index auf personal_tasks(type, source_type, source_id)
 * als Wahrheit + idempotenter Catch in FollowUpCreator::sync(). Zwei nebenläufige sync()-Aufrufe
 * (Doppel-Submit/Queue-Retry) erzeugen niemals zwei Zeilen.
 *
 * Enthält die im Bau entdeckte SOFT-DELETE-Kante: der Unique-Index zählt auch soft-gelöschte
 * Zeilen, der Pre-Check (SoftDelete-Scope) sieht sie nicht → der Catch muss inkl. trashed suchen
 * und wiederherstellen, statt zu 500en.
 */
class FollowUpRaceTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        DB::statement('SET FOREIGN_KEY_CHECKS=0'); // synthetische customer/employee-IDs
    }

    protected function tearDown(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        parent::tearDown();
    }

    private function sync(string $sourceType, int $sourceId, array $data = []): PersonalTask
    {
        return app(FollowUpCreator::class)->sync(
            $sourceType,
            $sourceId,
            ['customer_id' => 1, 'lead_product_list_id' => $sourceId, 'description' => 'race-test'],
            array_merge(['next_step' => 'Angebot erstellen', 'responsible' => [1]], $data),
            1
        );
    }

    private function keyCount(string $sourceType, int $sourceId, bool $withTrashed = false): int
    {
        $q = $withTrashed ? PersonalTask::withTrashed() : PersonalTask::query();

        return $q->where('type', 'follow_up')
            ->where('source_type', $sourceType)
            ->where('source_id', $sourceId)
            ->count();
    }

    /** Idempotenz (Pre-Check-Pfad): zwei sync() mit gleichem Schlüssel → genau EIN Task. */
    public function test_zwei_sync_erzeugen_genau_einen_task(): void
    {
        $this->sync('race_src', 5001);
        $this->sync('race_src', 5001);

        $this->assertSame(1, $this->keyCount('race_src', 5001));
    }

    /** DB-Constraint = Wahrheit: roher zweiter Insert mit gleichem Schlüssel wirft Unique-Violation. */
    public function test_db_constraint_blockt_rohes_duplikat(): void
    {
        $this->sync('race_src', 5002);

        $row = (array) DB::table('personal_tasks')
            ->where(['type' => 'follow_up', 'source_type' => 'race_src', 'source_id' => 5002])
            ->whereNull('deleted_at')->first();
        unset($row['id']);
        $row['created_at'] = now();
        $row['updated_at'] = now();

        $this->expectException(QueryException::class);
        DB::table('personal_tasks')->insert($row); // gleicher Schlüssel -> 23000
    }

    /**
     * RACE-CATCH (Live): eine kollidierende Zeile erscheint ZWISCHEN Pre-Check und create()
     * (per creating-Hook simuliert der "gewinnende" Thread) → sync() wirft NICHT, sondern lädt
     * neu und aktualisiert. Ergebnis: genau EIN Task.
     */
    public function test_race_catch_faengt_live_kollision_ab(): void
    {
        $injected = false;
        PersonalTask::creating(function (PersonalTask $t) use (&$injected) {
            if ($injected) {
                return; // Guard: Klon-Insert nicht rekursiv erneut injizieren
            }
            if ($t->type === 'follow_up' && $t->source_type === 'race_src' && (int) $t->source_id === 5003) {
                $injected = true;
                $t->replicate()->save(); // "Gewinner"-Zeile mit gleichem Schlüssel (Pre-Check hatte leer gesehen)
            }
        });

        $task = $this->sync('race_src', 5003); // create() kollidiert -> Catch -> re-lookup -> update

        $this->assertNotNull($task->id);
        $this->assertSame(1, $this->keyCount('race_src', 5003));
    }

    /**
     * SOFT-DELETE-KANTE (deterministischer Catch-Durchlauf): ein soft-gelöschtes Follow-up ist für
     * den Pre-Check unsichtbar, kollidiert aber am Unique-Index → der Catch findet es withTrashed,
     * stellt es wieder her und aktualisiert. Kein 500, genau EIN lebender Task.
     */
    public function test_soft_geloeschtes_followup_wird_wiederhergestellt_statt_500(): void
    {
        $first = $this->sync('race_src', 5004, ['next_step' => 'alt']);
        $first->delete(); // soft-delete
        $this->assertSame(0, $this->keyCount('race_src', 5004));                 // live: keins
        $this->assertSame(1, $this->keyCount('race_src', 5004, withTrashed: true)); // trashed: eins

        $again = $this->sync('race_src', 5004, ['next_step' => 'neu']); // darf NICHT 500en

        $this->assertSame(1, $this->keyCount('race_src', 5004));                    // wieder genau ein LEBENDER
        $this->assertFalse($again->fresh()->trashed());                          // wiederhergestellt
        $this->assertSame('neu', $again->fresh()->task_title);                   // aktualisiert (last-write-wins)
    }

    /** NULL-Quellen (manuelle Tasks) koexistieren — der Unique-Index blockt mehrere NULLs nicht. */
    public function test_null_quellen_koexistieren(): void
    {
        $mk = fn () => PersonalTask::create([
            'type' => 'personal_task', 'source_type' => null, 'source_id' => null,
            'task_title' => 'manuell', 'task_status' => 'open', 'assigned_by' => '1',
        ]);
        $a = $mk();
        $b = $mk();

        $this->assertNotSame($a->id, $b->id);
        $this->assertSame(2, PersonalTask::whereNull('source_type')->whereNull('source_id')
            ->where('task_title', 'manuell')->count());
    }
}
