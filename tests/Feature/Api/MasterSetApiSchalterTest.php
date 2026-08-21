<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Z2-W0-10 — Waechter fuer die reversible Stilllegung von `api/secure/master-sets*` (Y-11).
 *
 * Zwei Schalterstellungen, beide festgenagelt:
 *   A) `false` (Vorgabe): alle drei Routen antworten 404 — auch mit GUELTIGEN Zugangsdaten.
 *      Das "auch mit gueltigen" ist der eigentliche Punkt: waere hier 401 oder 403,
 *      verriete die Antwort die Existenz der Flaeche. Ein Test, der nur anonym prueft,
 *      koennte gruen sein, waehrend der Schalter gar nichts tut.
 *   B) `true`: das Verhalten von heute — anonym 401, mit Kopfzeilen 200. Der Rueckweg ist
 *      damit belegt und nicht nur behauptet.
 *
 * Rot-Probe (21.08.): Middleware von beiden Routen-Stellen abgehaengt -> beide A-Tests rot,
 * beide B-Tests gruen. Der Test kann also rot werden, und er wird es genau am Schalter.
 */
class MasterSetApiSchalterTest extends TestCase
{
    use DatabaseTransactions;

    private const BENUTZER = 'master_api_test';
    private const PASSWORT = 'master_api_test_passwort';

    /** Alle drei Routen des Auftrags — die Debug-Route liegt ausserhalb der Gruppe und wird gern vergessen. */
    private const ROUTEN = [
        '/api/secure/master-sets',
        '/api/secure/master-sets/1',
        '/api/secure/master-sets-debug',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        // Der Controller liest die Zugangsdaten ueber env() direkt, nicht ueber config().
        // $_SERVER wird vom ServerConst-Adapter gelesen und wirkt daher ohne .env-Datei.
        $_SERVER['MASTER_SET_API_USER'] = self::BENUTZER;
        $_SERVER['MASTER_SET_API_PASSWORD'] = self::PASSWORT;
    }

    protected function tearDown(): void
    {
        unset($_SERVER['MASTER_SET_API_USER'], $_SERVER['MASTER_SET_API_PASSWORD']);

        parent::tearDown();
    }

    private function mitZugangsdaten(): array
    {
        return [
            'X-API-USER' => self::BENUTZER,
            'X-API-PASSWORD' => self::PASSWORT,
        ];
    }

    public function test_schalter_aus_liefert_404_auf_allen_drei_routen_trotz_gueltiger_zugangsdaten(): void
    {
        // app.debug bewusst aus: mit eingeschaltetem Debug haengt Laravel an JEDEN Fehler
        // einen Stapelspeicher, in dem Route und Controller stehen. Was die Antwort verraet,
        // will ich in der Form messen, die live ausgeliefert wird — sonst misst der Test
        // eine Eigenschaft der Testumgebung statt eine des Schalters.
        config(['services.master_set_api.aktiv' => false, 'app.debug' => false]);

        foreach (self::ROUTEN as $route) {
            $antwort = $this->withHeaders($this->mitZugangsdaten())->getJson($route);

            $antwort->assertStatus(404);
            $this->assertStringNotContainsString(
                'master',
                strtolower((string) $antwort->getContent()),
                "Die Antwort auf {$route} darf die stillgelegte Flaeche nicht benennen.",
            );
        }
    }

    public function test_schalter_aus_liefert_404_auch_anonym(): void
    {
        config(['services.master_set_api.aktiv' => false]);

        foreach (self::ROUTEN as $route) {
            $this->getJson($route)->assertStatus(404);
        }
    }

    public function test_schalter_an_stellt_das_verhalten_von_heute_wieder_her(): void
    {
        config(['services.master_set_api.aktiv' => true]);

        // anonym: die Schnittstelle existiert wieder und verlangt Zugangsdaten
        $this->getJson('/api/secure/master-sets')->assertStatus(401);

        // mit Zugangsdaten: bedient
        $this->withHeaders($this->mitZugangsdaten())
            ->getJson('/api/secure/master-sets')
            ->assertStatus(200)
            ->assertJsonPath('ok', true);
    }

    public function test_vorgabe_ohne_env_ist_stillgelegt(): void
    {
        // Ohne gesetzte Umgebungsvariable muss der Schalter aus sein — eine Stilllegung,
        // die erst durch einen Eintrag wirksam wird, ist keine.
        $this->assertFalse(
            (bool) config('services.master_set_api.aktiv'),
            'Vorgabe von services.master_set_api.aktiv muss false sein.',
        );
    }
}
