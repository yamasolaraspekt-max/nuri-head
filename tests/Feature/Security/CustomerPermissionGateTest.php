<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * MASTER-01 P1-IDOR Customer/Belegkette-Bündel — Offer-/Customer-/Lead-Controller (update/delete) sind
 * jetzt hinter permission:Customer (enforced mit vorhandenen user_rolls-Grants). Vorher konnte jeder
 * Eingeloggte fremde Angebote/Reviews/Objekte/Dächer per ID ändern/löschen.
 */
class CustomerPermissionGateTest extends TestCase
{
    use DatabaseTransactions;

    private function user(bool $admin = false): User
    {
        return User::factory()->create(['password' => 'password', 'name' => (string) random_int(1, 9999), 'is_admin' => $admin]);
    }

    private function grant(User $u, string $item, array $flags): void
    {
        DB::table('user_rolls')->insert(array_merge([
            'user_id' => $u->id, 'item_id' => $item, 'is_read' => 0, 'is_add' => 0, 'is_update' => 0, 'is_delete' => 0,
            'created_at' => now(), 'updated_at' => now(),
        ], $flags));
    }

    // Plain-Parameter-Routen (kein Route-Model-Binding → permission-Middleware greift vor 404).
    public function test_ohne_customer_grant_kein_belegketten_zugriff(): void
    {
        $u = $this->user();
        $this->actingAs($u)->delete('/lead/roof/delete/1')->assertForbidden();                       // PVRoof
        $this->actingAs($u)->delete('/lead/mass-manager/mass-manager/delete/1')->assertForbidden();   // MassManager
    }

    public function test_mit_customer_delete_grant_durchgelassen(): void
    {
        $u = $this->user();
        $this->grant($u, 'Customer', ['is_delete' => 1]);
        $this->assertNotSame(403, $this->actingAs($u)->delete('/lead/roof/delete/999999')->getStatusCode());
    }

    public function test_admin_bypass(): void
    {
        $admin = $this->user(true);
        $this->assertNotSame(403, $this->actingAs($admin)->delete('/lead/roof/delete/999999')->getStatusCode());
    }

    /** Customer-Rest: Gott-Klassen (NewLeads/LeadOverview) + Appointments ebenfalls hinter permission:Customer. */
    public function test_customer_rest_ohne_grant_geblockt(): void
    {
        $u = $this->user();
        $this->actingAs($u)->get('/delete_lead_alternative/1')->assertForbidden();      // NewLeads@delete_alternative
        $this->actingAs($u)->delete('/lead-product/purge/1')->assertForbidden();         // LeadOverview@purge
    }
}
