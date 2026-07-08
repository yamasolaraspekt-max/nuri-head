<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * MASTER-01 P0-5 — Massenlöschungen (InquiryController/ProductPositionController/
 * CustomerMaintenanceContractController::bulkDelete) waren ungegatet (`whereIn('id',$ids)->delete()`
 * freier IDs). Jetzt `is_admin`-Gate (sichere Vorgabe für Massenlöschung).
 */
class BulkDeleteAdminGateTest extends TestCase
{
    use DatabaseTransactions;

    private function user(bool $admin): User
    {
        return User::factory()->create(['password' => 'password', 'name' => 'bd'.bin2hex(random_bytes(3)), 'is_admin' => $admin]);
    }

    public function test_nicht_admin_kann_nicht_massenloeschen(): void
    {
        $u = $this->user(false);
        $this->actingAs($u)->post('/inquiries/bulk-delete', ['ids' => [1, 2]])->assertForbidden();
        $this->actingAs($u)->post('/product-position/bulk-delete', ['ids' => [1]])->assertForbidden();
        $this->actingAs($u)->post('/admin/maintenance/contracts/bulk-delete', ['ids' => [1]])->assertForbidden();
    }

    public function test_admin_passiert_das_gate(): void
    {
        $admin = $this->user(true);
        $res = $this->actingAs($admin)->post('/inquiries/bulk-delete', ['ids' => []]);
        $this->assertNotSame(403, $res->getStatusCode());
    }
}
