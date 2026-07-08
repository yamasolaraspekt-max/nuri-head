<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * MASTER-01 P0-2 — die Legacy-Route `users/{user}/password` (UserController::updatePassword)
 * war ungegatet → Account-Takeover jedes Kontos. Jetzt hinter `permission:Users,update`
 * (mit is_admin-Bypass), wie der Zwilling adminUsersPassword.
 */
class UpdatePasswordGateTest extends TestCase
{
    use DatabaseTransactions;

    private function user(bool $admin): User
    {
        return User::factory()->create([
            'password' => Hash::make('orig-secret'), 'name' => 'u'.bin2hex(random_bytes(3)), 'is_admin' => $admin,
        ]);
    }

    public function test_nicht_admin_kann_fremdes_passwort_nicht_setzen(): void
    {
        $opfer = $this->user(false);
        $angreifer = $this->user(false); // kein is_admin, kein Users-Recht

        $res = $this->actingAs($angreifer)->post("/users/{$opfer->id}/password", [
            'password' => 'takeover-123', 'password_confirmation' => 'takeover-123',
        ]);
        $res->assertForbidden(); // 403 durch permission:Users,update

        // Passwort UNVERÄNDERT.
        $this->assertTrue(Hash::check('orig-secret', $opfer->fresh()->password));
    }

    public function test_admin_darf_passwort_setzen(): void
    {
        $admin = $this->user(true);
        $opfer = $this->user(false);

        $res = $this->actingAs($admin)->post("/users/{$opfer->id}/password", [
            'password' => 'neu-durch-admin-123', 'password_confirmation' => 'neu-durch-admin-123',
        ]);
        $this->assertNotSame(403, $res->getStatusCode()); // Admin-Bypass greift
        $this->assertTrue(Hash::check('neu-durch-admin-123', $opfer->fresh()->password));
    }
}
