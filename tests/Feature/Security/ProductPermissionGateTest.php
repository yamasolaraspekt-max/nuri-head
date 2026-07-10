<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * MASTER-01 P1-IDOR Product/Stammdaten+Lager-Bündel — Katalog-/Inventar-Controller (update/delete)
 * hinter permission:Product. Vorher konnte jeder Eingeloggte Katalog/Lager per ID ändern/löschen.
 */
class ProductPermissionGateTest extends TestCase
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

    public function test_ohne_product_grant_kein_katalog_zugriff(): void
    {
        $u = $this->user();
        $this->actingAs($u)->get('/temp_destroy/1')->assertForbidden();
        $this->actingAs($u)->get('/heating_type_destroy/1')->assertForbidden();
        $this->actingAs($u)->get('/building_type_destroy/1')->assertForbidden();
    }

    public function test_mit_product_delete_grant_durchgelassen(): void
    {
        $u = $this->user();
        $this->grant($u, 'Product', ['is_delete' => 1]);
        $this->assertNotSame(403, $this->actingAs($u)->get('/temp_destroy/999999')->getStatusCode());
    }

    public function test_admin_bypass(): void
    {
        $admin = $this->user(true);
        $this->assertNotSame(403, $this->actingAs($admin)->get('/temp_destroy/999999')->getStatusCode());
    }
}
