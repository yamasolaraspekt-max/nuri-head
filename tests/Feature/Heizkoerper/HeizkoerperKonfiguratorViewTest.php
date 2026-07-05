<?php

namespace Tests\Feature\Heizkoerper;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * (M4-a v-b) Heizkörper-Konfigurator-Fläche (Alpine). Rendering + Flag-Gate.
 * JS-Interaktion selbst wird über die bereits getesteten Endpunkte (v-a) abgesichert —
 * kein Headless-Browser im Bestand (bekannte Grenze).
 */
class HeizkoerperKonfiguratorViewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['features.heizkoerper' => true]);
    }

    private function user(): User
    {
        return User::factory()->create(['password' => 'password']);
    }

    public function test_konfigurator_rendert_mit_alpine_wurzel_und_csrf(): void
    {
        $r = $this->actingAs($this->user())->get(route('heizkoerper.konfigurator'));

        $r->assertOk();
        $r->assertSee('heizkoerperKonfigurator(', false);       // Alpine-Komponente
        $r->assertSee('x-data', false);                          // Alpine-Wurzel
        $r->assertSee('alpinejs', false);                        // Alpine wird (nur hier) geladen
        $r->assertSee(route('heizkoerper.berechnen'), false);    // fetch-Ziel v-a
        $r->assertSee(route('heizkoerper.kompatibilitaet'), false);
        $r->assertSee('name="csrf-token"', false);               // CSRF-Setup aus Layout
        $r->assertSee('Kandidaten nach Regelwerk', false);       // ehrliches Datenlage-Label vorhanden
    }

    public function test_konfigurator_404_bei_flag_off(): void
    {
        config(['features.heizkoerper' => false]);

        $this->actingAs($this->user())->get(route('heizkoerper.konfigurator'))->assertNotFound();
    }

    /** v-c-1: Stückliste-Sektion vorhanden + Datenlage-ehrliches Label (kein erfundener Preis). */
    public function test_stueckliste_sektion_und_datenlage_label(): void
    {
        $r = $this->actingAs($this->user())->get(route('heizkoerper.konfigurator'));

        $r->assertOk();
        $r->assertSee('Stückliste', false);
        $r->assertSee('stuecklistePositionen', false);                    // Positions-Builder (Anzeige)
        $r->assertSee('Regel-Kandidat — kein Preis/SKU', false);          // ehrliches Label regel-kandidaten
        $r->assertSee('Preise/SKU folgen aus dem Lieferanten-Import', false);
        $r->assertSee("p.preis === null ? '—' : p.preis", false);         // '—' statt 0,00-€-Optik
    }
}
