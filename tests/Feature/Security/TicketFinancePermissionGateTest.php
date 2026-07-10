<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/** MASTER-01 P1-IDOR — Ticket/Kundendienst (permission:Problem) + Anfragen (Inquiry) + Finanzen (Finance). */
class TicketFinancePermissionGateTest extends TestCase
{
    use DatabaseTransactions;

    private function user(bool $admin = false): User
    {
        return User::factory()->create(['password' => 'password', 'name' => (string) random_int(1, 9999), 'is_admin' => $admin]);
    }
    private function grant(User $u, string $item, array $flags): void
    {
        DB::table('user_rolls')->insert(array_merge(['user_id' => $u->id, 'item_id' => $item,
            'is_read' => 0, 'is_add' => 0, 'is_update' => 0, 'is_delete' => 0, 'created_at' => now(), 'updated_at' => now()], $flags));
    }

    public function test_ohne_grant_geblockt(): void
    {
        $u = $this->user();
        $this->actingAs($u)->get('/problem_destroy/1')->assertForbidden();
        $this->actingAs($u)->get('/inquiry_type_destroy/1')->assertForbidden();
    }

    public function test_mit_problem_grant_durch(): void
    {
        $u = $this->user();
        $this->grant($u, 'Problem', ['is_delete' => 1]);
        $this->assertNotSame(403, $this->actingAs($u)->get('/problem_destroy/999999')->getStatusCode());
    }

    public function test_admin_bypass(): void
    {
        $this->assertNotSame(403, $this->actingAs($this->user(true))->get('/problem_destroy/999999')->getStatusCode());
    }
}
