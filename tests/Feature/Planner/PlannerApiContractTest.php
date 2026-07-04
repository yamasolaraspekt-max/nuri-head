<?php

namespace Tests\Feature\Planner;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Planner-API Kontrakttests (nuriva-Zone) — Phase 1, Techniker-Kernpfad.
 * Gegen die REALEN Endpunkte + belegten Kontrakt (docs: Kontrakt-Befund):
 *   auth/token (login+password, 422 bei falsch), auth/me, logout,
 *   my-work (403 ohne Employee), items/{item}/complete-report (done, 422, 404).
 * Nur Test-Code, keine Produktivänderung.
 */
class PlannerApiContractTest extends TestCase
{
    use RefreshDatabase;

    private const ABILITIES = ['planner:read', 'planner:write', 'planner:attendance', 'planner:kanban'];

    /** Employee + verknüpfter User (User.name = employee.id -> authEmployeeId). */
    private function makeUserEmployee(string $password = 'password'): array
    {
        $empId = DB::table('employees')->insertGetId([
            'title' => 'Test Tech', 'name' => 'Mobile', 'lastname' => 'Tester', 'status' => 'Active',
            'email' => 'emp.' . uniqid() . '@example.com',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $user = User::create([
            'name' => (string) $empId,                 // numerisch -> authEmployeeId() = $empId
            'email' => 'user.' . uniqid() . '@example.com',
            'password' => Hash::make($password),
        ]);

        return [$user, $empId];
    }

    private function makeCustomer(): int
    {
        return DB::table('new_leads')->insertGetId([
            'customer_type' => 'Privat', 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    private function makePlanItem(int $customerId, ?int $employeeId): array
    {
        $planId = DB::table('planner_plans')->insertGetId([
            'customer_id' => $customerId, 'stage' => 'montage', 'status' => 'active',
            'title' => 'Test-Plan', 'created_at' => now(), 'updated_at' => now(),
        ]);

        $itemId = DB::table('planner_items')->insertGetId([
            'plan_id' => $planId, 'title' => 'Montage-Aufgabe', 'status' => 'open',
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

    // ---------------------------------------------------------------- auth/token

    public function test_token_valid_login_returns_token(): void
    {
        [$user] = $this->makeUserEmployee();

        $res = $this->postJson('/api/planner/auth/token', [
            'login' => $user->email, 'password' => 'password',
        ]);

        $res->assertStatus(200)
            ->assertJsonPath('ok', true)
            ->assertJsonPath('token_type', 'Bearer');
        $this->assertNotEmpty($res->json('token'));
    }

    public function test_token_wrong_password_returns_422(): void
    {
        [$user] = $this->makeUserEmployee();

        $this->postJson('/api/planner/auth/token', [
            'login' => $user->email, 'password' => 'falsch',
        ])->assertStatus(422);
    }

    public function test_token_missing_fields_returns_422(): void
    {
        $this->postJson('/api/planner/auth/token', [])->assertStatus(422);
    }

    // ---------------------------------------------------------------- auth/me + logout

    public function test_me_with_token_returns_ok(): void
    {
        [$user] = $this->makeUserEmployee();
        Sanctum::actingAs($user, self::ABILITIES);

        $this->getJson('/api/planner/auth/me')->assertStatus(200)->assertJsonPath('ok', true);
    }

    public function test_me_without_token_is_401(): void
    {
        $this->getJson('/api/planner/auth/me')->assertStatus(401);
    }

    public function test_logout_deletes_current_token(): void
    {
        [$user] = $this->makeUserEmployee();

        $token = $this->postJson('/api/planner/auth/token', ['login' => $user->email, 'password' => 'password'])->json('token');

        $this->assertDatabaseCount('personal_access_tokens', 1); // durch auth/token erzeugt

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/planner/auth/logout')->assertStatus(200)->assertJsonPath('ok', true);

        // Dokumentierter Kontrakt (:117 "Deletes only the current token"): der Token-Datensatz ist weg.
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    // ---------------------------------------------------------------- my-work

    public function test_my_work_returns_assigned_item(): void
    {
        [$user, $empId] = $this->makeUserEmployee();
        $customer = $this->makeCustomer();
        $this->makePlanItem($customer, $empId);

        Sanctum::actingAs($user, self::ABILITIES);

        $this->getJson('/api/planner/my-work')
            ->assertStatus(200)
            ->assertJsonPath('ok', true)
            ->assertJsonFragment(['title' => 'Montage-Aufgabe']);
    }

    public function test_my_work_without_employee_is_403(): void
    {
        // User.name nicht-numerisch -> authEmployeeId() = null -> 403
        $user = User::create(['name' => 'kein-mitarbeiter', 'email' => 'x.' . uniqid() . '@example.com', 'password' => Hash::make('password')]);
        Sanctum::actingAs($user, self::ABILITIES);

        $this->getJson('/api/planner/my-work')->assertStatus(403)->assertJsonPath('ok', false);
    }

    public function test_my_work_without_token_is_401(): void
    {
        $this->getJson('/api/planner/my-work')->assertStatus(401);
    }

    // ---------------------------------------------------------------- complete-report

    public function test_complete_report_marks_item_done(): void
    {
        [$user, $empId] = $this->makeUserEmployee();
        $customer = $this->makeCustomer();
        [, $itemId] = $this->makePlanItem($customer, $empId);

        Sanctum::actingAs($user, self::ABILITIES);

        $this->patchJson("/api/planner/items/{$itemId}/complete-report", [
            'report' => 'Montage abgeschlossen, alles dicht.',
        ])->assertStatus(200)->assertJsonPath('ok', true);

        $this->assertDatabaseHas('planner_items', ['id' => $itemId, 'status' => 'done']);
    }

    public function test_complete_report_empty_without_skip_is_422(): void
    {
        [$user, $empId] = $this->makeUserEmployee();
        $customer = $this->makeCustomer();
        [, $itemId] = $this->makePlanItem($customer, $empId);

        Sanctum::actingAs($user, self::ABILITIES);

        $this->patchJson("/api/planner/items/{$itemId}/complete-report", [
            'report' => '', 'skip_report' => false,
        ])->assertStatus(422);

        $this->assertDatabaseHas('planner_items', ['id' => $itemId, 'status' => 'open']);
    }

    public function test_complete_report_empty_with_skip_marks_done(): void
    {
        [$user, $empId] = $this->makeUserEmployee();
        $customer = $this->makeCustomer();
        [, $itemId] = $this->makePlanItem($customer, $empId);

        Sanctum::actingAs($user, self::ABILITIES);

        $this->patchJson("/api/planner/items/{$itemId}/complete-report", [
            'report' => '', 'skip_report' => true,
        ])->assertStatus(200)->assertJsonPath('ok', true);

        $this->assertDatabaseHas('planner_items', ['id' => $itemId, 'status' => 'done']);
    }

    public function test_complete_report_unassigned_item_is_404(): void
    {
        [$user] = $this->makeUserEmployee();
        $customer = $this->makeCustomer();
        [, $itemId] = $this->makePlanItem($customer, null); // KEINE Pivot-Zuordnung

        Sanctum::actingAs($user, self::ABILITIES);

        $this->patchJson("/api/planner/items/{$itemId}/complete-report", [
            'report' => 'x',
        ])->assertStatus(404);
    }

    public function test_complete_report_without_token_is_401(): void
    {
        $this->patchJson('/api/planner/items/1/complete-report', ['report' => 'x'])->assertStatus(401);
    }
}
