<?php

namespace Tests\Feature\Planner;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Z2-W0-5 — Zuständigkeitsbindung der Nuriva-API `api/planner/*`.
 *
 * **Der Ist-Zustand vor diesem Bau:** alle 20 `api/planner`-Routen trugen `Authenticate:sanctum`
 * und sonst nichts — keine Policy, kein Gate, kein globaler Scope, `grep -c "permission:"` auf
 * `routes/api.php` ergab 0. Vier Stellen liefen offen:
 *   A-1 `employees/{employee}/work|day-report` — fremde Kennung ungeprüft, Antwort mit GPS.
 *   A-2 `customer-images` (upload + index) — `customer_id` nur `exists`.
 *   A-3 `items/…/master-sets` link/unlink und `plans/{plan}/…/add` — Binding ohne Scope.
 *   A-4 `items/{item}/materials` index/store — kein Ownership, Melder vom Client bestimmt.
 *
 * **Warum das mehr als die Mobil-App betrifft:** jedes Konto mit Passwort erhält einen Token mit
 * allen vier Abilities, und `sanctum.stateful` enthält `ticket.test` — jede eingeloggte
 * Browser-Sitzung erreicht dieselben Endpunkte per Cookie, ganz ohne Token. Deshalb prüft
 * Kriterium H ausdrücklich den Cookie-Weg.
 *
 * **Die Trennlinie, die diese Datei festnagelt** (Entscheidungsblatt 21.08., „Sehen" ≠ „Fälschen"):
 * A-1 ist Sehen und folgt dem Rechte-Schalter aus W0-7 — steht er auf `true`, sieht heute jeder
 * jeden Tagesbericht. A-2/A-3/A-4 sind Schreiben und Zuschreiben; sie bleiben gesperrt, **auch
 * wenn der Schalter offen steht**. Beide Richtungen werden unten gemessen, nicht behauptet.
 *
 * **Nicht hier:** die Vorgesetztenkette (`resolveReviewer`) ist Stufe 2 und hängt an **Y-9**.
 * Die Matrix-Zeile A nennt „Vorgesetzter → 200"; das ist mit Stufe 1 nicht gebaut und wird hier
 * folglich auch nicht behauptet. Gebaut ist die Stufe-1-Regel aus dem Nachtrag (Kriterium G).
 *
 * Läuft ausschließlich gegen `ticket_testing` (`phpunit.xml` erzwingt `DB_DATABASE`).
 */
class PlannerApiZustaendigkeitTest extends TestCase
{
    use DatabaseTransactions;

    private const ABILITIES = ['planner:read', 'planner:write', 'planner:attendance', 'planner:kanban'];

    protected function setUp(): void
    {
        parent::setUp();

        // Vorgabe für jede Zusage: Schalter ZU. Ein Tor, das nur bei offenem Schalter geprüft
        // wird, ist nicht geprüft. Die Zusagen, die den offenen Schalter messen, setzen ihn selbst.
        config(['rechte.alle_fuer_alle' => false]);
    }

    /** Employee + verknüpfter User (`users.name` trägt die `employees.id`). */
    private function mitarbeiterKonto(bool $admin = false): array
    {
        $empId = DB::table('employees')->insertGetId([
            'title' => 'W05', 'name' => 'Zust', 'lastname' => 'Test', 'status' => 'Active',
            'email' => 'emp.' . uniqid() . '@example.com',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $user = User::create([
            'name' => (string) $empId,
            'email' => 'user.' . uniqid() . '@example.com',
            'password' => Hash::make('password'),
            'is_admin' => $admin ? 1 : 0,
        ]);

        return [$user, $empId];
    }

    private function kunde(): int
    {
        return DB::table('new_leads')->insertGetId([
            'customer_type' => 'Privat', 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    /** Plan + Punkt; `$employeeId` wird dem Punkt zugeordnet (null = niemandem). */
    private function planUndPunkt(int $customerId, ?int $employeeId): array
    {
        $planId = DB::table('planner_plans')->insertGetId([
            'customer_id' => $customerId, 'stage' => 'montage', 'status' => 'active',
            'title' => 'W05-Plan', 'created_at' => now(), 'updated_at' => now(),
        ]);

        $itemId = DB::table('planner_items')->insertGetId([
            'plan_id' => $planId, 'title' => 'W05-Punkt', 'status' => 'open',
            'planned_start_at' => now()->format('Y-m-d 08:00:00'), 'sort_order' => 1,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        if ($employeeId !== null) {
            DB::table('planner_item_employees')->insert([
                'planner_item_id' => $itemId, 'employee_id' => $employeeId, 'role' => 'lead',
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        return [$planId, $itemId];
    }

    private function masterSet(): int
    {
        // `master_sets.article_group_id` ist NOT NULL ohne Vorgabe — eine vorhandene Gruppe
        // nehmen, sonst eine anlegen. Test-Seeds bleiben in `ticket_testing`.
        $gruppe = (int) (DB::table('article_groups')->value('id') ?? 0);

        if ($gruppe <= 0) {
            $gruppe = DB::table('article_groups')->insertGetId([
                'article_group' => 'W05-Gruppe', 'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        return DB::table('master_sets')->insertGetId([
            'name' => 'W05-Set ' . uniqid(), 'status' => 'Published',
            'article_group_id' => $gruppe,
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    // =====================================================================
    // Kriterium A + G — A-1: fremder Mitarbeiter
    // =====================================================================

    /** Kriterium A/G, Sperre: fremde Mitarbeiter-Kennung → 403 auf beiden A-1-Wegen. */
    public function test_a1_fremder_mitarbeiter_ist_verboten(): void
    {
        [$ich] = $this->mitarbeiterKonto();
        [, $fremdEmp] = $this->mitarbeiterKonto();

        Sanctum::actingAs($ich, self::ABILITIES);

        $this->getJson("/api/planner/employees/{$fremdEmp}/work")->assertStatus(403);
        $this->getJson("/api/planner/employees/{$fremdEmp}/day-report")->assertStatus(403);
    }

    /** Kriterium A, Eigen-Pfad: die Nuriva-App lädt eigene Daten und darf nicht brechen. */
    public function test_a1_eigener_mitarbeiter_geht_durch(): void
    {
        [$ich, $meineEmp] = $this->mitarbeiterKonto();

        Sanctum::actingAs($ich, self::ABILITIES);

        $this->getJson("/api/planner/employees/{$meineEmp}/work")->assertStatus(200);
        $this->getJson("/api/planner/employees/{$meineEmp}/day-report")->assertStatus(200);
    }

    /** Kriterium G, zweite Achse: Admin kommt durch, auch bei geschlossenem Schalter. */
    public function test_a1_admin_darf_fremde_sehen(): void
    {
        [$admin] = $this->mitarbeiterKonto(admin: true);
        [, $fremdEmp] = $this->mitarbeiterKonto();

        Sanctum::actingAs($admin, self::ABILITIES);

        $this->getJson("/api/planner/employees/{$fremdEmp}/work")->assertStatus(200);
    }

    /**
     * Kriterium G, die Schalter-Achse — SEHEN folgt dem Schalter.
     *
     * Das ist keine Lücke, sondern Yamas Entscheidung vom 21.08. („alle Rechte für alle", Y-9).
     * Die Zusage hält fest, dass das Tor genau dieser Regel folgt und nicht heimlich schärfer
     * oder weicher ist als beschlossen. Sie wird rot, wenn jemand den Schalter aus der
     * Sehen-Achse herausnimmt — dann ist das eine Entscheidung und keine Nebenwirkung.
     */
    public function test_a1_offener_rechte_schalter_oeffnet_das_sehen(): void
    {
        config(['rechte.alle_fuer_alle' => true]);

        [$ich] = $this->mitarbeiterKonto();
        [, $fremdEmp] = $this->mitarbeiterKonto();

        Sanctum::actingAs($ich, self::ABILITIES);

        $this->getJson("/api/planner/employees/{$fremdEmp}/work")->assertStatus(200);
    }

    /**
     * Kriterium H — der Cookie-Weg.
     *
     * `sanctum.stateful` enthält `ticket.test`: eine eingeloggte Browser-Sitzung erreicht
     * `api/planner/*` ohne jeden Token. Ein Tor, das nur den Token-Weg schützt, schützt nichts.
     */
    public function test_h_cookie_weg_ohne_token_ist_ebenso_gebunden(): void
    {
        [$ich] = $this->mitarbeiterKonto();
        [, $fremdEmp] = $this->mitarbeiterKonto();

        $this->actingAs($ich)
            ->getJson("/api/planner/employees/{$fremdEmp}/work")
            ->assertStatus(403);
    }

    // =====================================================================
    // Kriterium B — A-2: Kundenakte
    // =====================================================================

    /** Kriterium B, Sperre: fremde `customer_id` → 403, lesend wie schreibend. */
    public function test_a2_fremde_kundenakte_ist_verboten(): void
    {
        Storage::fake('local');

        [$ich, $meineEmp] = $this->mitarbeiterKonto();
        $meinKunde = $this->kunde();
        $this->planUndPunkt($meinKunde, $meineEmp);

        $fremderKunde = $this->kunde();
        $this->planUndPunkt($fremderKunde, null);

        Sanctum::actingAs($ich, self::ABILITIES);

        $this->getJson("/api/planner/customer-images?customer_id={$fremderKunde}")->assertStatus(403);

        $this->post('/api/planner/customer-images/upload', [
            'customer_id' => $fremderKunde,
            'file' => UploadedFile::fake()->image('fremd.jpg'),
        ])->assertStatus(403);
    }

    /** Kriterium B, Eigen-Pfad: die zugeordnete Akte bleibt offen. */
    public function test_a2_zugeordnete_kundenakte_geht_durch(): void
    {
        [$ich, $meineEmp] = $this->mitarbeiterKonto();
        $meinKunde = $this->kunde();
        $this->planUndPunkt($meinKunde, $meineEmp);

        Sanctum::actingAs($ich, self::ABILITIES);

        $this->getJson("/api/planner/customer-images?customer_id={$meinKunde}")->assertStatus(200);
    }

    // =====================================================================
    // Kriterium C — A-3: fremder Plan / fremder Punkt
    // =====================================================================

    /** Kriterium C, Sperre: link, unlink und addToPlan an fremdem Punkt/Plan → 403. */
    public function test_a3_fremder_plan_und_punkt_sind_verboten(): void
    {
        [$ich] = $this->mitarbeiterKonto();
        [$fremdPlan, $fremdItem] = $this->planUndPunkt($this->kunde(), null);
        $set = $this->masterSet();

        Sanctum::actingAs($ich, self::ABILITIES);

        $this->postJson("/api/planner/items/{$fremdItem}/master-sets/{$set}")->assertStatus(403);
        $this->deleteJson("/api/planner/items/{$fremdItem}/master-sets/{$set}")->assertStatus(403);
        $this->postJson("/api/planner/plans/{$fremdPlan}/master-sets/{$set}/add")->assertStatus(403);
    }

    /** Kriterium C, Eigen-Pfad. */
    public function test_a3_eigener_plan_und_punkt_gehen_durch(): void
    {
        [$ich, $meineEmp] = $this->mitarbeiterKonto();
        [$meinPlan, $meinItem] = $this->planUndPunkt($this->kunde(), $meineEmp);
        $set = $this->masterSet();

        Sanctum::actingAs($ich, self::ABILITIES);

        $this->postJson("/api/planner/items/{$meinItem}/master-sets/{$set}")->assertStatus(200);
        $this->postJson("/api/planner/plans/{$meinPlan}/master-sets/{$set}/add")->assertStatus(200);
    }

    /**
     * Kriterium C, und zugleich die Trennlinie: der offene Rechte-Schalter öffnet das
     * SCHREIBEN NICHT. Wäre `hasPermission()` auch in der Zuständigkeitsachse, stünde hier 200 —
     * und ein Schalter für Sichtbarkeit hätte fremde Pläne zum Schreiben freigegeben.
     */
    public function test_a3_offener_schalter_oeffnet_das_schreiben_nicht(): void
    {
        config(['rechte.alle_fuer_alle' => true]);

        [$ich] = $this->mitarbeiterKonto();
        [$fremdPlan, $fremdItem] = $this->planUndPunkt($this->kunde(), null);
        $set = $this->masterSet();

        Sanctum::actingAs($ich, self::ABILITIES);

        $this->postJson("/api/planner/items/{$fremdItem}/master-sets/{$set}")->assertStatus(403);
        $this->postJson("/api/planner/plans/{$fremdPlan}/master-sets/{$set}/add")->assertStatus(403);
    }

    // =====================================================================
    // Kriterium D — A-4: fremder Punkt und Melder-Spoofing
    // =====================================================================

    /** Kriterium D, Sperre: fremder Auftragspunkt → 403, lesend wie schreibend. */
    public function test_a4_fremdes_item_ist_verboten(): void
    {
        [$ich] = $this->mitarbeiterKonto();
        [, $fremdItem] = $this->planUndPunkt($this->kunde(), null);

        Sanctum::actingAs($ich, self::ABILITIES);

        $this->getJson("/api/planner/items/{$fremdItem}/materials")->assertStatus(403);
        $this->postJson("/api/planner/items/{$fremdItem}/materials", ['name' => 'Kabel'])->assertStatus(403);
    }

    /**
     * Kriterium D, der Kern — Melder-Spoofing.
     *
     * Am eigenen Punkt, mit einem abweichenden `requested_by_employee_id` im Rumpf: der
     * Datensatz muss die eigene Kennung tragen. Vorher wurde der Client-Wert dem eigenen
     * ausdrücklich VORGEZOGEN (`?? $this->authEmployeeId()` war nur der Rückfall).
     */
    public function test_a4_melder_kommt_aus_der_sitzung_nicht_vom_client(): void
    {
        [$ich, $meineEmp] = $this->mitarbeiterKonto();
        [, $fremdEmp] = $this->mitarbeiterKonto();
        [, $meinItem] = $this->planUndPunkt($this->kunde(), $meineEmp);

        Sanctum::actingAs($ich, self::ABILITIES);

        $name = 'W05-Material-' . uniqid();

        $this->postJson("/api/planner/items/{$meinItem}/materials", [
            'name' => $name,
            'requested_by_employee_id' => $fremdEmp,
        ])->assertStatus(200);

        $gespeichert = DB::table('planner_item_material_requests')
            ->where('planner_item_id', $meinItem)
            ->orderByDesc('id')
            ->first();

        $this->assertNotNull($gespeichert, 'Ohne Datensatz misst die Zusage nichts.');
        $this->assertSame(
            $meineEmp,
            (int) $gespeichert->requested_by_employee_id,
            'Der Melder muss der angemeldete Mitarbeiter sein.',
        );
        $this->assertNotSame(
            $fremdEmp,
            (int) $gespeichert->requested_by_employee_id,
            'Der Client-Wert darf den Melder nicht bestimmen.',
        );
    }

    /** Kriterium D, Trennlinie: auch bei offenem Schalter bleibt Fälschen Fälschen. */
    public function test_a4_offener_schalter_erlaubt_kein_faelschen(): void
    {
        config(['rechte.alle_fuer_alle' => true]);

        [$ich, $meineEmp] = $this->mitarbeiterKonto();
        [, $fremdEmp] = $this->mitarbeiterKonto();
        [, $meinItem] = $this->planUndPunkt($this->kunde(), $meineEmp);

        Sanctum::actingAs($ich, self::ABILITIES);

        $this->postJson("/api/planner/items/{$meinItem}/materials", [
            'name' => 'W05-Schalter-' . uniqid(),
            'requested_by_employee_id' => $fremdEmp,
        ])->assertStatus(200);

        $this->assertSame(
            $meineEmp,
            (int) DB::table('planner_item_material_requests')
                ->where('planner_item_id', $meinItem)->orderByDesc('id')->value('requested_by_employee_id'),
        );
    }

    // =====================================================================
    // Kriterium E — ein Baustein, keine vier Kopien
    // =====================================================================

    /**
     * Kriterium E, als Ratsche statt als einmaliger grep.
     *
     * Vier Controller ziehen denselben Trait; keiner bringt eine eigene Zuständigkeitsregel mit.
     * Die Zusage wird rot, sobald jemand eine fünfte Kopie der Regel anlegt statt den Baustein
     * zu benutzen — genau der Wildwuchs, den `authEmployeeId()` in sechs Controllern schon zeigt.
     */
    public function test_e_genau_ein_baustein_vier_aufrufer(): void
    {
        $trait = \App\Support\Planner\PlannerZustaendigkeit::class;

        foreach ([
            \App\Http\Controllers\Planner\PlannerEmployeeApiController::class,
            \App\Http\Controllers\Planner\PlannerMobileCustomerImageController::class,
            \App\Http\Controllers\Planner\PlannerMasterSetController::class,
            \App\Http\Controllers\Planner\PlannerItemMaterialController::class,
        ] as $klasse) {
            $this->assertContains(
                $trait,
                class_uses_recursive($klasse),
                "{$klasse} benutzt den gemeinsamen Baustein nicht.",
            );
        }
    }
}
