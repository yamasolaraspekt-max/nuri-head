<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Z2-W0-1 — Gebäudeakte `/objekte/*` hinter `permission:Customer,read`.
 *
 * **Die Lücke, die das schließt:** die drei Routen lagen im Block `['web','auth']` ohne
 * `permission`. `ObjektakteController@index` paginiert ALLE `LeadAlternativeAdd` mit Kundenname,
 * Firma und Kundennummer; `scopeGebaeudeSuche` filtert per LIKE über den Kundennamen ohne Bindung
 * an den Nutzer. Ergebnis vor diesem Bau: **`GET /objekte?q=a` lieferte jedem Eingeloggten eine
 * durchsuchbare Kundenliste** — die Sidebar behauptete das Recht (`'permission' => 'Customer'`),
 * die Route prüfte es nicht.
 *
 * **Gemessen, nicht angenommen:** die Middleware greift VOR dem Route-Model-Binding — auch
 * `/objekte/999999` antwortet ohne Recht mit 403, nicht mit 404. Deshalb reichen hier
 * Fantasie-IDs; es muss kein Objekt angelegt werden.
 *
 * Läuft ausschließlich gegen `ticket_testing` (`phpunit.xml` erzwingt `DB_DATABASE`).
 *
 * **BEIDE SCHALTERSTELLUNGEN, seit Z2-W0-7 geschlossen:** Yamas Entscheidung vom 21.08.
 * (`docs/regelwerk/ENTSCHEIDUNG-RECHTE-ALLE-FUER-ALLE.md`) verlangt, dass die Welle-0-Tore mit
 * **und** ohne `rechte.alle_fuer_alle` geprüft werden. Diese Datei trug den Vermerk, dass ihr die
 * zweite Hälfte fehlt, solange der Schalter nicht existiert. Er existiert seit Z2-W0-7; die
 * Hälfte steht jetzt unten. **Der Vorgabewert in der Testumgebung ist `false`** — sonst würde der
 * Schalter jede Tor-Zusage grün färben, und keine wüsste mehr, ob ihr Tor schließt.
 */
class ObjektakteGateTest extends TestCase
{
    use DatabaseTransactions;

    private function user(bool $admin = false): User
    {
        return User::factory()->create([
            'password' => 'password',
            'name' => (string) random_int(1, 9999),
            'is_admin' => $admin ? 1 : 0,
        ]);
    }

    private function grant(User $u, string $item, array $flags): void
    {
        DB::table('user_rolls')->insert(array_merge([
            'user_id' => $u->id, 'item_id' => $item,
            'is_read' => 0, 'is_add' => 0, 'is_update' => 0, 'is_delete' => 0,
            'created_at' => now(), 'updated_at' => now(),
        ], $flags));
    }

    /** Kriterium B — ohne `Customer` ist keine der drei Routen erreichbar. */
    public function test_ohne_customer_recht_alle_drei_routen_verboten(): void
    {
        $u = $this->user();
        $this->actingAs($u)->get('/objekte')->assertForbidden();
        $this->actingAs($u)->get('/objekte/999999')->assertForbidden();
        $this->actingAs($u)->post('/objekte/999999/auslegung')->assertForbidden();
    }

    /**
     * Kriterium C — Bestandsnutzer verlieren nichts. Ohne diese Zusage wäre auch eine Sperre grün,
     * die ALLE aussperrt; genau das ist der Fehlsperr-Fall aus dem Rückweg des Auftrags.
     */
    public function test_mit_customer_read_kein_verbot(): void
    {
        $u = $this->user();
        $this->grant($u, 'Customer', ['is_read' => 1]);
        $this->assertNotSame(403, $this->actingAs($u)->get('/objekte')->getStatusCode());
        $this->assertNotSame(403, $this->actingAs($u)->get('/objekte/999999')->getStatusCode());
    }

    /** Der Admin-Weg bleibt offen — dieselbe Zusage wie im Vorbild `CustomerPermissionGateTest`. */
    public function test_admin_bypass(): void
    {
        $admin = $this->user(true);
        $this->assertNotSame(403, $this->actingAs($admin)->get('/objekte')->getStatusCode());
    }

    /**
     * Die andere Schalterstellung: mit `alle_fuer_alle=true` kommt derselbe rechtelose Nutzer
     * durch — **ohne dass die Route-Middleware entfernt wurde.** Genau das ist die Zusage des
     * Entscheidungsblatts: „die Gates werden weiter gebaut und in beiden Schalterstellungen
     * getestet, damit der Tag, an dem Yama differenzieren will, kein Neubau ist."
     */
    public function test_mit_schalter_alle_fuer_alle_kein_verbot(): void
    {
        config(['rechte.alle_fuer_alle' => true]);
        $this->assertNotSame(403, $this->actingAs($this->user())->get('/objekte')->getStatusCode());
    }

    /** Und die Gegenprobe in derselben Datei: mit Schalter AUS sperrt dasselbe Tor wieder. */
    public function test_mit_schalter_aus_wieder_verboten(): void
    {
        config(['rechte.alle_fuer_alle' => false]);
        $this->actingAs($this->user())->get('/objekte')->assertForbidden();
    }
}
