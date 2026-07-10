<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * MASTER-01 P1-IDOR Task-Owner — personal_tasks: nur Ersteller (assigned_by) ODER Zugewiesene
 * (employees-Pivot) duerfen aendern/loeschen (Yama-Entscheidung). Vorher: jeder Eingeloggte.
 */
class PersonalTaskOwnerGateTest extends TestCase
{
    use DatabaseTransactions;

    private function user(int $emp, bool $admin = false): User
    {
        return User::factory()->create(['password' => 'password', 'name' => (string) $emp, 'is_admin' => $admin]);
    }

    private function task(int $ownerEmp): int
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        $id = DB::table('personal_tasks')->insertGetId([
            'assigned_by' => $ownerEmp, 'task_title' => 'geheim', 'task_status' => 'start',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        return $id;
    }

    public function test_fremder_kann_aufgabe_nicht_loeschen(): void
    {
        $taskId = $this->task(5001);
        $fremder = $this->user(6002);
        $this->actingAs($fremder)->delete("/personal-tasks/{$taskId}/destroy")->assertForbidden();
        $this->assertNull(DB::table('personal_tasks')->where('id', $taskId)->value('deleted_at'));
    }

    public function test_ersteller_darf(): void
    {
        $taskId = $this->task(5001);
        $owner = $this->user(5001);
        $this->assertNotSame(403, $this->actingAs($owner)->delete("/personal-tasks/{$taskId}/destroy")->getStatusCode());
    }

    public function test_admin_bypass(): void
    {
        $taskId = $this->task(5001);
        $this->assertNotSame(403, $this->actingAs($this->user(9009, true))->delete("/personal-tasks/{$taskId}/destroy")->getStatusCode());
    }
}
