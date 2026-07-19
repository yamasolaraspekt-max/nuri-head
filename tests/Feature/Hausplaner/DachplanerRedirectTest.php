<?php

namespace Tests\Feature\Hausplaner;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Abriss Alt-Dachplaner (2026-07-19): der Prototyp (public/planer/planer.js) ist entfernt;
 * die alte URL bleibt als Redirect auf das Hausplaner-Studio erhalten (Bookmarks).
 * Dieser Test bewacht den Redirect — und damit, dass die alte View NICHT mehr ausgeliefert wird.
 */
class DachplanerRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_alte_dachplaner_url_leitet_auf_studio_um(): void
    {
        $admin = User::factory()->create(['password' => 'password', 'is_admin' => 1]);

        $this->actingAs($admin)->get('/admin/hausplaner/dachplaner')
            ->assertRedirect(route('hausplaner.studio'));
    }

    public function test_gast_wird_zum_login_geleitet(): void
    {
        $this->get('/admin/hausplaner/dachplaner')->assertRedirect('/login');
    }
}
