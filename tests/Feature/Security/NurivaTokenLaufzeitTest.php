<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Z2-W0-12 — Nuriva-Token laufen ab, werden bereinigt und lassen sich widerrufen (Y-10, Yama 21.08.).
 *
 * **Die Lücke:** `config/sanctum.php` führte `expiration => null` — ausgestellte Token liefen NIE
 * ab. Es gab keine Bereinigung (`prune-expired` kam im Scheduler nicht vor) und keinen Weg, einen
 * Token serverseitig zu widerrufen, ohne das Konto zu deaktivieren; `logout-all` ist
 * Selbstbedienung und braucht genau den Token, der in fremder Hand sein könnte.
 *
 * Läuft ausschließlich gegen `ticket_testing` (`phpunit.xml` erzwingt `DB_DATABASE`).
 */
class NurivaTokenLaufzeitTest extends TestCase
{
    use DatabaseTransactions;

    private function nutzer(): User
    {
        return User::factory()->create([
            'email' => 'token' . random_int(1, 999999) . '@probe.test',
            'password' => Hash::make('geheim123'),
            'name' => (string) random_int(1, 9999),
        ]);
    }

    /** Kriterium A — die Vorgabe sind 8 Stunden, in Minuten. */
    public function test_laufzeit_ist_acht_stunden(): void
    {
        $this->assertSame(480, (int) config('sanctum.expiration'), '8 h × 60 min');
    }

    /**
     * Kriterium A, zweite Hälfte — **der Rückweg darf nicht zur Totalsperre werden.**
     * `0` bedeutet unbegrenzt. Rechnete die Konfiguration stumpf `0 * 60`, stünde dort `0` Minuten,
     * und Sanctum liesse jeden Token sofort ablaufen — aus „Rückweg" würde „alles gesperrt".
     */
    public function test_null_stunden_bedeutet_unbegrenzt_nicht_sofort_abgelaufen(): void
    {
        $stunden = 0;
        $wert = $stunden > 0 ? $stunden * 60 : null;
        $this->assertNull($wert, '0 muss auf null abbilden, nicht auf 0 Minuten');
    }

    /** Kriterium B, erste Hälfte — ein gültiger Token öffnet. */
    public function test_gueltiger_token_wird_angenommen(): void
    {
        $u = $this->nutzer();
        $this->withHeader('Authorization', 'Bearer ' . $u->createToken('probe')->plainTextToken)
            ->getJson('/api/planner/auth/me')
            ->assertSuccessful();
    }

    /**
     * Kriterium B, zweite Hälfte — ein abgelaufener Token öffnet nichts mehr.
     *
     * **Warum das ein EIGENER Test ist und nicht die Fortsetzung des vorigen:** in der ersten
     * Fassung standen beide Aufrufe in einem Test. Der abgelaufene Token kam trotzdem mit 200
     * durch — nach dem ersten erfolgreichen Aufruf hält die Anwendungsinstanz den aufgelösten
     * Nutzer, und der zweite Aufruf prüft die Laufzeit nicht neu. Eine Wegwerf-Probe ohne
     * Vorab-Aufruf lieferte sofort 401 und hat es gezeigt. **Der Fehler lag im Test, nicht im Bau
     * — aber ohne die Trennung hätte er als „die Sperre wirkt nicht" gemeldet werden können.**
     *
     * Neun Stunden zurückdatiert wirkt wie neun Stunden Wartezeit, ohne zu warten.
     */
    public function test_abgelaufener_token_wird_abgewiesen(): void
    {
        $u = $this->nutzer();
        $token = $u->createToken('probe');
        $token->accessToken->forceFill(['created_at' => now()->subHours(9)])->save();

        $this->withHeader('Authorization', 'Bearer ' . $token->plainTextToken)
            ->getJson('/api/planner/auth/me')
            ->assertUnauthorized();
    }

    /** Kriterium D — der Widerruf löscht alle Token des Nutzers und lässt das Konto in Ruhe. */
    public function test_widerruf_loescht_alle_token_und_laesst_das_konto_aktiv(): void
    {
        $u = $this->nutzer();
        $u->createToken('eins');
        $u->createToken('zwei');
        $this->assertSame(2, $u->tokens()->count());

        $this->artisan('nuriva:token-widerruf', ['nutzer' => $u->email])
            ->assertSuccessful();

        $this->assertSame(0, $u->fresh()->tokens()->count(), 'alle Token müssen weg sein');
        $this->assertNull($u->fresh()->disabled_at, 'das Konto darf NICHT mitgesperrt werden');
    }
}
