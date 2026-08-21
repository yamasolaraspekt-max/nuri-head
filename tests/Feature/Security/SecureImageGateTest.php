<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Z2-W0-8 — Kundenbilder hinter `permission:Customer,read`.
 *
 * **Die Lücke:** `/secure-image/id/{id}`, `/secure-image/file/{filename}` und
 * `/image/secure/{id}` lagen in der Gruppe `['web','auth']` ohne Rechteprüfung — ein beliebiger
 * Login genügte, um Kundenfotos und -dokumente zu laden. `secureImage` prüfte gar nichts,
 * `secureDownloadScreenshot` lud den Datensatz **vor** der Auth-Prüfung.
 *
 * **Beide Schalterstellungen**, wie das Entscheidungsblatt vom 21.08. es verlangt: mit
 * `rechte.alle_fuer_alle = false` wirkt das Tor, mit `true` kommt derselbe rechtelose Nutzer
 * durch — ohne dass die Middleware entfernt wurde.
 *
 * Läuft ausschließlich gegen `ticket_testing` (`phpunit.xml` erzwingt `DB_DATABASE`).
 */
class SecureImageGateTest extends TestCase
{
    use DatabaseTransactions;

    private function nutzer(): User
    {
        return User::factory()->create([
            'password' => 'password', 'name' => (string) random_int(1, 9999), 'is_admin' => 0,
        ]);
    }

    private function grant(User $u): void
    {
        DB::table('user_rolls')->insert([
            'user_id' => $u->id, 'item_id' => 'Customer',
            'is_read' => 1, 'is_add' => 0, 'is_update' => 0, 'is_delete' => 0,
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    /** Kriterium B, erste Hälfte — Schalter aus, kein Recht: alle drei Wege verboten. */
    public function test_schalter_aus_ohne_customer_verboten(): void
    {
        config(['rechte.alle_fuer_alle' => false]);
        $u = $this->nutzer();
        $this->actingAs($u)->get('/secure-image/id/999999')->assertForbidden();
        $this->actingAs($u)->get('/secure-image/file/egal.png')->assertForbidden();
        $this->actingAs($u)->get('/image/secure/999999')->assertForbidden();
    }

    /**
     * Kriterium B, zweite Hälfte — mit Recht greift das Tor nicht mehr. Die Antwort ist hier
     * **404**, weil es das Bild nicht gibt; entscheidend ist, dass sie NICHT 403 ist.
     */
    public function test_schalter_aus_mit_customer_kein_verbot(): void
    {
        config(['rechte.alle_fuer_alle' => false]);
        $u = $this->nutzer();
        $this->grant($u);
        $this->assertNotSame(403, $this->actingAs($u)->get('/secure-image/id/999999')->getStatusCode());
    }

    /** Kriterium B, dritte Hälfte — Schalter an: derselbe rechtelose Nutzer kommt durch. */
    public function test_schalter_an_ohne_customer_kein_verbot(): void
    {
        config(['rechte.alle_fuer_alle' => true]);
        $this->assertNotSame(403, $this->actingAs($this->nutzer())->get('/secure-image/id/999999')->getStatusCode());
    }

    /**
     * Eine Zeichenkette als ID endet mit 404.
     *
     * **Diese Zusage belegt `whereNumber` NICHT allein, und das gehört gesagt:** in der Rot-Probe
     * blieb sie auch ohne die Einschränkung grün, weil `findOrFail('abc')` ebenfalls 404 liefert.
     * Der Unterschied liegt davor — mit `whereNumber` trifft der Aufruf die Route gar nicht erst
     * und löst keine Abfrage aus. Sie steht hier als Charakterisierung des Verhaltens, nicht als
     * Beweis der Einschränkung; wer sie dafür hält, überschätzt sie.
     */
    public function test_nicht_numerische_id_trifft_die_route_nicht(): void
    {
        config(['rechte.alle_fuer_alle' => true]);
        $this->actingAs($this->nutzer())->get('/secure-image/id/abc')->assertNotFound();
    }
}
