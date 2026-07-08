<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * MASTER-01 P0-4 — Belegkette-Löschungen (OfferController::destroy Hard-Delete des Angebotsbaums,
 * NewLeadsController deleteObject/destroyWithReason/destroy Kunden-/Objekt-Kaskade) waren nur `auth`,
 * kein Gate → jeder Eingeloggte konnte fremde Angebote/Kunden/Objekte löschen. Jetzt
 * `hasPermission('Customer','delete')` (mit is_admin-Bypass), wie das Vorbild DealController::authorizeDealDelete.
 */
class BelegketteDeleteGateTest extends TestCase
{
    use DatabaseTransactions;

    private function user(bool $admin): User
    {
        return User::factory()->create(['password' => 'password', 'name' => 'bk'.bin2hex(random_bytes(3)), 'is_admin' => $admin]);
    }

    public function test_nicht_berechtigter_kann_belegkette_nicht_loeschen(): void
    {
        $u = $this->user(false);
        $this->actingAs($u)->get('/new_lead_delete/1')->assertForbidden();        // Lead löschen (kein Model-Binding → Guard greift)
        $this->actingAs($u)->post('/new_lead_delete/1', ['reason' => 'x'])->assertForbidden(); // destroyWithReason
        $this->actingAs($u)->delete('/admin/offers/1')->assertForbidden();        // Angebotsbaum (int-id → Guard greift)
        // Hinweis: deleteObject (/lead/objects/{object}) nutzt Route-Model-Binding → nicht-existente ID = 404
        // vor dem Guard; der Guard ist gesetzt (php-verifiziert) und greift bei existierendem Objekt mit 403.
    }

    public function test_admin_passiert_das_gate(): void
    {
        $admin = $this->user(true);
        $res = $this->actingAs($admin)->get('/new_lead_delete/999999');
        $this->assertNotSame(403, $res->getStatusCode()); // is_admin-Bypass
    }
}
