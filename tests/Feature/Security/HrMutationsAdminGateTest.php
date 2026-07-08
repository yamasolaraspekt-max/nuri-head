<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * MASTER-01 P0-3 — HR/Lohn/Medizin-Mutationen (EmployeeController profile_update/updatePasscode/
 * destroy/update, LeaveController approve/destroy/…, EmployeeSickController store/update/destroy)
 * waren nur `auth`, kein Rollen-Gate → jeder Eingeloggte konnte fremde Lohn-/Urlaubs-/Krankheitsdaten
 * ändern. Jetzt `is_admin`-Gate (sichere Vorgabe; dedizierte HR-Rolle folgt als Yama-Entscheidung).
 */
class HrMutationsAdminGateTest extends TestCase
{
    use DatabaseTransactions;

    private function user(bool $admin): User
    {
        return User::factory()->create([
            'password' => 'password', 'name' => 'hr'.bin2hex(random_bytes(3)), 'is_admin' => $admin,
        ]);
    }

    public function test_nicht_admin_wird_bei_hr_mutationen_geblockt(): void
    {
        $u = $this->user(false);

        $this->actingAs($u)->get('/leave_approve/1')->assertForbidden();
        $this->actingAs($u)->patch('/employee_passcode/1', ['passcode' => '1234'])->assertForbidden();
        $this->actingAs($u)->post('/employee_profile_update', ['id' => 1])->assertForbidden();
        $this->actingAs($u)->delete('/employee-sick/destroy/1')->assertForbidden();
    }

    public function test_admin_wird_nicht_durch_das_gate_geblockt(): void
    {
        $admin = $this->user(true);
        // Admin passiert das is_admin-Gate; danach ggf. 404/302/Validierung, aber NICHT 403.
        $res = $this->actingAs($admin)->get('/leave_approve/999999');
        $this->assertNotSame(403, $res->getStatusCode());
    }
}
