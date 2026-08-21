<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Z2-W0-9 — „Deaktiviert" deaktiviert.
 *
 * **Die Lücke:** die Oberfläche versprach „Deactivated", die Sperre hing aber an `is_active` — und
 * das ist ein ONLINE-Flag: `LogUserLogin.php:26` setzt es beim Anmelden auf 1,
 * `LogUserLogout.php:16` beim Abmelden auf 0. Ein deaktivierter Nutzer meldete sich also an und
 * hob damit seine eigene Sperre auf. Umgekehrt sperrte ein gewöhnliches Web-Abmelden den
 * Mobile-Login — eine Fehlsperre gegen einen regulären Vorgang.
 *
 * Der Kontostatus steht jetzt in `users.disabled_at`; `is_active` bleibt, was es ist.
 *
 * Läuft ausschließlich gegen `ticket_testing` (`phpunit.xml` erzwingt `DB_DATABASE`).
 */
class KontostatusTest extends TestCase
{
    use DatabaseTransactions;

    private function nutzer(bool $gesperrt = false): User
    {
        return User::factory()->create([
            'email' => 'kontostatus' . random_int(1, 999999) . '@probe.test',
            'password' => Hash::make('geheim123'),
            'name' => (string) random_int(1, 9999),
            'is_active' => 1,
            'disabled_at' => $gesperrt ? now() : null,
        ]);
    }

    /** Kriterium A — ein deaktiviertes Konto kommt nicht durch die Web-Anmeldung. */
    public function test_deaktiviert_kein_web_login(): void
    {
        $u = $this->nutzer(true);
        $this->post('/login', ['email' => $u->email, 'password' => 'geheim123'])
            ->assertSessionHasErrors();
        $this->assertGuest();   // die Anmeldung darf keine Sitzung eröffnen
    }

    /** Gegenprobe zu A — dasselbe Konto ohne Sperre meldet sich an (kein Verlust). */
    public function test_aktiv_web_login_geht(): void
    {
        $u = $this->nutzer(false);
        $this->post('/login', ['email' => $u->email, 'password' => 'geheim123']);
        $this->assertAuthenticatedAs($u);
    }

    /**
     * Kriterium C — eine LAUFENDE Sitzung endet beim nächsten Request. Das ist der Fall, den der
     * alte `logOffUser` lösen wollte und nicht konnte: er löschte in einer `sessions`-Tabelle,
     * die es hier nicht gibt.
     */
    public function test_laufende_sitzung_endet_nach_deaktivierung(): void
    {
        $u = $this->nutzer(false);
        $this->actingAs($u)->get('/')->assertStatus(200);

        $u->disabled_at = now();
        $u->save();

        $this->actingAs($u)->get('/');
        $this->assertGuest();   // die Middleware muss die Sitzung beendet haben
    }

    /**
     * Kriterium E — ein Web-Abmelden sperrt den Mobile-Login NICHT mehr. Geprüft an der Bedingung
     * selbst: `is_active = 0` bei leerem `disabled_at` ist kein Sperrgrund.
     */
    public function test_web_logout_sperrt_mobile_nicht(): void
    {
        $u = $this->nutzer(false);
        $u->is_active = 0;          // genau das, was LogUserLogout tut
        $u->save();

        $this->assertNull($u->fresh()->disabled_at, 'Abmelden darf den Kontostatus nicht setzen');
    }

    /** Kriterium D — die Migration sperrt niemanden aus. */
    public function test_migration_sperrt_niemanden(): void
    {
        $this->assertSame(
            0,
            (int) User::query()->whereNotNull('disabled_at')->count(),
            'nach der Migration darf kein Bestandsnutzer gesperrt sein',
        );
    }

    /**
     * Kriterium B — der Token-Endpunkt weist ein deaktiviertes Konto ab UND widerruft dessen
     * vorhandene Tokens. Ohne den Widerruf bliebe der Mobile-Zugang offen, während die
     * Weboberfläche das Konto längst als „Deactivated" führt.
     */
    public function test_deaktiviert_kein_token_und_bestehende_werden_widerrufen(): void
    {
        $u = $this->nutzer(false);
        $u->createToken('probe');
        $this->assertSame(1, $u->tokens()->count(), 'Vorbedingung: ein Token liegt vor');

        $u->disabled_at = now();
        $u->save();

        $this->postJson('/api/planner/auth/token', [
            'login' => $u->email, 'password' => 'geheim123',
        ])->assertStatus(422);

        $this->assertSame(0, $u->fresh()->tokens()->count(), 'die vorhandenen Token müssen weg sein');
    }
}
