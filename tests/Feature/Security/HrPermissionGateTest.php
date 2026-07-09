<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * MASTER-01 P1-IDOR HR-Bündel — Employee/Profile/*-Controller sind jetzt hinter permission:Employee
 * (enforced mit den vorhandenen user_rolls-Grants). Vorher konnte jeder Eingeloggte HR-Stammdaten
 * (Holiday/Skill/Contract/Country/Language/Team/Address/…) anlegen/ändern/löschen.
 */
class HrPermissionGateTest extends TestCase
{
    use DatabaseTransactions;

    private function user(bool $admin = false): User
    {
        return User::factory()->create(['password' => 'password', 'name' => (string) random_int(1, 9999), 'is_admin' => $admin]);
    }

    private function grant(User $u, string $item, array $flags): void
    {
        DB::table('user_rolls')->insert(array_merge([
            'user_id' => $u->id, 'item_id' => $item,
            'is_read' => 0, 'is_add' => 0, 'is_update' => 0, 'is_delete' => 0,
            'created_at' => now(), 'updated_at' => now(),
        ], $flags));
    }

    public function test_ohne_employee_grant_kein_hr_zugriff(): void
    {
        $u = $this->user(); // kein is_admin, kein Grant
        $this->actingAs($u)->get('/skill_delete/1')->assertForbidden();
        $this->actingAs($u)->delete('/country_destroy/1')->assertForbidden();
        $this->actingAs($u)->delete('/holiday_destroy/1')->assertForbidden();
        $this->actingAs($u)->get('/emergency_delete/1')->assertForbidden();
    }

    public function test_mit_employee_delete_grant_durchgelassen(): void
    {
        $u = $this->user();
        $this->grant($u, 'Employee', ['is_delete' => 1]);
        // Gate passiert; danach ggf. 404/302 (ID existiert nicht), aber NICHT 403.
        $this->assertNotSame(403, $this->actingAs($u)->get('/skill_delete/999999')->getStatusCode());
    }

    public function test_admin_bypass(): void
    {
        $admin = $this->user(true);
        $this->assertNotSame(403, $this->actingAs($admin)->get('/skill_delete/999999')->getStatusCode());
    }
}
