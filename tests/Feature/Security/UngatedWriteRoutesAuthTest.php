<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * MASTER-01 P0-1 — vormals anonyme Schreibrouten (Route-Gruppen `['middleware'=>'web']` ohne auth)
 * sind jetzt hinter `auth`. Prüft: anonym → Login-Redirect; Kiosk/Webhook bleiben öffentlich.
 *
 * GET-Routen gewählt (CSRF-frei), damit der Test die AUTH-Schicht isoliert prüft (nicht CSRF).
 */
class UngatedWriteRoutesAuthTest extends TestCase
{
    use DatabaseTransactions;

    /** Vormals ungegatete interne Routen: anonym ⇒ Redirect auf Login (kein 200). */
    public function test_interne_routen_sind_jetzt_hinter_auth(): void
    {
        foreach ([
            '/emp_address_delete/1',   // Employee\Address (HR)
            '/skill_delete/1',         // Skills (HR)
            '/emergency_delete/1',     // Notfallkontakt (HR)
            '/employee_image_get/1/x', // Employee-Dokumente (HR)
        ] as $uri) {
            $this->get($uri)->assertRedirect('/login');
        }
    }

    /** Kiosk-/Webhook-Routen bleiben bewusst öffentlich (kein Auth-Redirect). */
    public function test_kiosk_und_webhook_bleiben_oeffentlich(): void
    {
        // employee.qr.reader ist GET + öffentlich (Kiosk). Darf NICHT auf /login umleiten.
        $res = $this->get('/employee/qr/code/reader/in');
        $this->assertNotSame('/login', $res->headers->get('Location'),
            'Kiosk-Route darf nicht hinter auth landen.');
    }

    /** Eingeloggter Nutzer wird NICHT auf Login umgeleitet (Auth-Schicht lässt ihn durch). */
    public function test_eingeloggt_kein_login_redirect(): void
    {
        $user = User::factory()->create(['password' => 'password', 'name' => 'sec-p01', 'is_admin' => true]);
        $res = $this->actingAs($user)->get('/emp_address_delete/999999');
        // Egal ob 200/404/302-woanders — nur NICHT der Login-Redirect.
        $this->assertNotSame('/login', $res->headers->get('Location'));
    }
}
