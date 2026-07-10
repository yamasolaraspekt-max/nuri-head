<?php

namespace Tests\Feature\Kanban;

use App\Models\LeadStage;
use App\Models\User;
use App\Services\FollowUp\FollowUpCreator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

/**
 * A1 — „verdrahten statt Neubau": Zieht eine Karte in die Angebots-Phase (Stage-Key `offer`),
 * entsteht GENAU EINE Angebots-Aufgabe ueber den bestehenden FollowUpCreator (UPSERT je
 * source_type/source_id). Der additive Block sitzt NACH der committeten DB::transaction und in
 * eigenem try/catch — ein Task-Fehler darf den Move nie in eine 500-Response verwandeln.
 */
class LeadStageAngebotTaskTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        // Der Kern-Move broadcastet (Reverb/Pusher) — im Test kein Netzwerk-Broker.
        // Auf den Null-Treiber umlenken, damit der Move-Kern nicht am Broker haengt.
        config(['broadcasting.default' => 'null']);

        // personal_tasks/lead_product_lists haengen an FKs (customer/alternative/product).
        // Wir arbeiten mit synthetischen IDs -> FK-Checks fuer diese Verbindung aussetzen
        // (wie bestehende Tests). tearDown setzt zurueck.
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
    }

    protected function tearDown(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        parent::tearDown();
    }

    /** Sorgt dafuer, dass ein Stage-Key aktiv/vorhanden ist (stageExists() + stageMap()). */
    private function ensureStage(string $key, string $name): void
    {
        $stage = LeadStage::withTrashed()->where('key', $key)->first();

        if ($stage) {
            $stage->forceFill([
                'is_active'  => true,
                'deleted_at' => null,
                'name'       => $stage->name !== '' ? $stage->name : $name,
            ])->save();

            return;
        }

        LeadStage::create(['key' => $key, 'name' => $name, 'is_active' => true]);
    }

    private function admin(string $employeeId = '1'): User
    {
        // In diesem Projekt speichert users.name die employees.id (numerisch).
        return User::factory()->create([
            'name'     => $employeeId,
            'password' => 'password',
            'is_admin' => true,
        ]);
    }

    private function seedStages(): void
    {
        $this->ensureStage('lead', 'Lead');
        $this->ensureStage('offer', 'Angebot');
        $this->ensureStage('deal', 'Auftrag');
        $this->ensureStage('project', 'Montage');
    }

    /** Legt eine lead_product_list-Zeile an und liefert [id, cid, aid, pid]. */
    private function makeLead(string $status = 'lead'): array
    {
        static $seq = 0;
        $seq++;
        $cid = 900000 + $seq;
        $aid = 800000 + $seq;
        $pid = 700000 + $seq;

        // FK-Checks sind fuer diese Verbindung ausgesetzt (setUp) -> plain insert.
        // KEIN Schema::withoutForeignKeyConstraints hier: das wuerde die Checks am Ende
        // wieder auf 1 setzen und den nachfolgenden Controller-Insert brechen.
        $id = DB::table('lead_product_lists')->insertGetId([
            'customer_id'    => $cid,
            'alternative_id' => $aid,
            'product_id'     => $pid,
            'status'         => $status,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        return [$id, $cid, $aid, $pid];
    }

    private function changeStage(int $cid, int $aid, int $pid, int $leadId, string $stage, array $teams = []): \Illuminate\Testing\TestResponse
    {
        return $this->postJson("/lead-product/change-stage/{$cid}/{$aid}/{$pid}", [
            'stage'           => $stage,
            'lead_product_id' => $leadId,
            'teams'           => $teams,
        ]);
    }

    private function angebotTasks(int $leadId)
    {
        return DB::table('personal_tasks')
            ->where('type', 'follow_up')
            ->where('source_type', 'lead_product_list')
            ->where('source_id', $leadId)
            ->whereNull('deleted_at')
            ->get();
    }

    // ---------------------------------------------------------------------
    // 1. Kern: Move lead -> offer erzeugt genau eine Angebots-Aufgabe
    // ---------------------------------------------------------------------
    public function test_move_nach_offer_erzeugt_genau_eine_angebots_aufgabe(): void
    {
        $this->seedStages();
        [$id, $cid, $aid, $pid] = $this->makeLead('lead');

        $this->actingAs($this->admin('1'));

        $res = $this->changeStage($cid, $aid, $pid, $id, 'offer', [2, 3]);
        $res->assertOk()->assertJson(['success' => true]);

        $tasks = $this->angebotTasks($id);
        $this->assertCount(1, $tasks, 'Es muss GENAU eine Angebots-Aufgabe existieren.');

        $task = $tasks->first();
        $this->assertSame('Angebot erstellen', $task->task_title);
        $this->assertSame('follow_up', $task->type);
        $this->assertSame('open', $task->task_status);

        // due_date ~ now()+3 Werktage und ist selbst ein Werktag
        $expected = now()->addWeekdays(3)->toDateString();
        $due = Carbon::parse($task->due_date);
        $this->assertSame($expected, $due->toDateString(), 'Faelligkeit = now()+3 Werktage.');
        $this->assertTrue($due->isWeekday(), 'Faelligkeit muss ein Werktag sein.');

        // Zuweisung an die uebergebenen Team-Mitglieder (Pivot je Person)
        $assignees = DB::table('employees_personal_tasks')
            ->where('task_id', $task->id)
            ->pluck('employee_id')
            ->map(fn ($v) => (int) $v)
            ->sort()
            ->values()
            ->all();
        $this->assertSame([2, 3], $assignees, 'Aufgabe an JEDES uebergebene Team-Mitglied.');
    }

    // ---------------------------------------------------------------------
    // 2. Idempotenz (UPSERT): offer -> lead -> offer bleibt EINE Zeile
    // ---------------------------------------------------------------------
    public function test_upsert_offer_lead_offer_bleibt_eine_zeile(): void
    {
        $this->seedStages();
        [$id, $cid, $aid, $pid] = $this->makeLead('lead');

        $this->actingAs($this->admin('1'));

        $this->changeStage($cid, $aid, $pid, $id, 'offer', [2])->assertOk();
        $this->assertCount(1, $this->angebotTasks($id));

        $this->changeStage($cid, $aid, $pid, $id, 'lead', [2])->assertOk();
        $this->changeStage($cid, $aid, $pid, $id, 'offer', [2, 3])->assertOk();

        $this->assertCount(1, $this->angebotTasks($id), 'Zweiter offer-Move darf KEINE zweite Zeile erzeugen.');
    }

    // ---------------------------------------------------------------------
    // 3. Nur der richtige Uebergang: Move nach deal erzeugt KEINE Aufgabe
    // ---------------------------------------------------------------------
    public function test_move_nach_deal_erzeugt_keine_angebots_aufgabe(): void
    {
        $this->seedStages();
        [$id, $cid, $aid, $pid] = $this->makeLead('lead');

        $this->actingAs($this->admin('1'));

        $this->changeStage($cid, $aid, $pid, $id, 'deal', [2])->assertOk();

        $this->assertCount(0, $this->angebotTasks($id), 'Kein offer-Uebergang -> keine Angebots-Aufgabe.');
    }

    // ---------------------------------------------------------------------
    // 4. Re-Save innerhalb offer (oldStage===offer) triggert nicht
    // ---------------------------------------------------------------------
    public function test_re_save_innerhalb_offer_triggert_nicht(): void
    {
        $this->seedStages();
        // Lead startet bereits in offer, OHNE bestehende Aufgabe.
        [$id, $cid, $aid, $pid] = $this->makeLead('offer');

        $this->actingAs($this->admin('1'));

        $this->changeStage($cid, $aid, $pid, $id, 'offer', [2])->assertOk();

        $this->assertCount(0, $this->angebotTasks($id), 'oldStage===offer -> Bedingung false -> keine Aufgabe.');
    }

    // ---------------------------------------------------------------------
    // 5. FEHLERFALL-LOG (Frage 3): Task-Fehler beschaedigt den Move NICHT,
    //    Fehler wird SICHTBAR geloggt (lead_id + reason), Response bleibt success.
    // ---------------------------------------------------------------------
    public function test_task_fehler_beschaedigt_move_nicht_und_wird_geloggt(): void
    {
        $this->seedStages();
        [$id, $cid, $aid, $pid] = $this->makeLead('lead');

        // FollowUpCreator-Mock, dessen sync() wirft.
        $throwing = \Mockery::mock(FollowUpCreator::class);
        $throwing->shouldReceive('sync')->andThrow(new \RuntimeException('boom-task-fehler'));
        $this->app->instance(FollowUpCreator::class, $throwing);

        Log::spy();

        $this->actingAs($this->admin('1'));

        $res = $this->changeStage($cid, $aid, $pid, $id, 'offer', [2]);

        // (a) Response ist success (kein 500) trotz Task-Fehler
        $res->assertOk()->assertJson(['success' => true]);

        // (b) Move ist WIRKLICH committet
        $status = DB::table('lead_product_lists')->where('id', $id)->value('status');
        $this->assertSame('offer', $status, 'Der Move muss trotz Task-Fehler committet bleiben.');

        // (c) Sichtbarer Log::error mit lead_product_list_id + reason
        Log::shouldHaveReceived('error')
            ->withArgs(function ($message, $context = []) use ($id) {
                return $message === 'A1: Angebots-Follow-up konnte nicht erzeugt werden'
                    && (int) ($context['lead_product_list_id'] ?? 0) === $id
                    && ($context['reason'] ?? null) === 'boom-task-fehler';
            })
            ->once();

        // Und keine Aufgabe entstand (Mock hat nichts geschrieben)
        $this->assertCount(0, $this->angebotTasks($id));
    }
}
