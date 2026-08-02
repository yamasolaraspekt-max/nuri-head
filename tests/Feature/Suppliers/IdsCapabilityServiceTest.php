<?php

namespace Tests\Feature\Suppliers;

use App\Models\SupplierConnection;
use App\Services\Suppliers\Ids\IdsCapabilityService;
use App\Services\Suppliers\SupplierConnectionTestService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class IdsCapabilityServiceTest extends TestCase
{
    use RefreshDatabase;

    private const PASSWORT_FIXTURE = 'STRENG-GEHEIMES-PW-FIXTURE-983';

    /** §5.6 der Norm, woertlich. */
    private const LI_XML = <<<'XML'
<Logininformationen>
    <Kundennummer_erforderlich>false</Kundennummer_erforderlich>
    <Benutzername_erforderlich>true</Benutzername_erforderlich>
    <Passwort_erforderlich>true</Passwort_erforderlich>
</Logininformationen>
XML;

    /** §5.7 der Norm, woertlich. */
    private const SV_XML = <<<'XML'
<Schnittstellenversionen>
    <Version>1.3</Version>
    <Version>2.0</Version>
    <Version>2.1</Version>
    <Version>2.2</Version>
    <Version>2.3</Version>
    <Version>2.5</Version>
</Schnittstellenversionen>
XML;

    private function verbindung(array $attribute = []): SupplierConnection
    {
        return SupplierConnection::create(array_merge([
            'name' => 'IDS Testshop',
            'supplier_key' => 'ids-testshop',
            'connector_type' => 'ids',
            'endpoint_url' => 'https://shop.example/ids',
            'is_active' => true,
            'password' => self::PASSWORT_FIXTURE,
        ], $attribute));
    }

    private function dienst(): IdsCapabilityService
    {
        return app(IdsCapabilityService::class);
    }

    private static function queryVon(Request $request): array
    {
        parse_str((string) parse_url($request->url(), PHP_URL_QUERY), $query);

        return $query;
    }

    private function fakeMitNormAntworten(): void
    {
        Http::fake(function (Request $request) {
            $query = self::queryVon($request);

            return match ($query['action'] ?? null) {
                'LI' => Http::response(self::LI_XML, 200),
                'SV' => Http::response(self::SV_XML, 200),
                default => Http::response('OK', 200),
            };
        });
    }

    // ── K-01 / K-02: genau ein Parameter ─────────────────────────────────────

    public function test_k01_li_anfrage_traegt_genau_action_li(): void
    {
        $this->fakeMitNormAntworten();

        $this->dienst()->loginInfo($this->verbindung());

        Http::assertSent(function (Request $request) {
            return $request->method() === 'GET'
                && self::queryVon($request) === ['action' => 'LI'];
        });
        Http::assertSentCount(1);
    }

    public function test_k02_sv_anfrage_traegt_genau_action_sv(): void
    {
        $this->fakeMitNormAntworten();

        $this->dienst()->versionen($this->verbindung());

        Http::assertSent(function (Request $request) {
            return $request->method() === 'GET'
                && self::queryVon($request) === ['action' => 'SV'];
        });
        Http::assertSentCount(1);
    }

    // ── K-03: die Normbeispiele werden korrekt gelesen, Befund wird abgelegt ─

    public function test_k03_normbeispiele_werden_korrekt_gelesen_und_abgelegt(): void
    {
        $this->fakeMitNormAntworten();
        $verbindung = $this->verbindung();

        $befund = $this->dienst()->ermitteln($verbindung);

        $this->assertFalse($befund->kundennummerErforderlich);
        $this->assertTrue($befund->benutzernameErforderlich);
        $this->assertTrue($befund->passwortErforderlich);
        $this->assertTrue($befund->liUnterstuetzt);
        $this->assertTrue($befund->svUnterstuetzt);
        $this->assertSame(['1.3', '2.0', '2.1', '2.2', '2.3', '2.5'], $befund->versionen);
        $this->assertSame([], $befund->versionenUnbekannt);

        $verbindung->refresh();
        $this->assertNotNull($verbindung->capabilities_checked_at);
        $this->assertTrue($verbindung->capabilities['li_unterstuetzt']);
        $this->assertSame(['1.3', '2.0', '2.1', '2.2', '2.3', '2.5'], $verbindung->capabilities['versionen']);
    }

    // ── Die 15 Kantenfaelle ──────────────────────────────────────────────────

    public function test_kante01_html_statt_xml_ergibt_befund_ohne_throw(): void
    {
        Http::fake(['*' => Http::response('<html><body>Shopstartseite</body></html>', 200)]);

        $befund = $this->dienst()->ermitteln($this->verbindung());

        $this->assertFalse($befund->liUnterstuetzt);
        $this->assertNotNull($befund->hinweis);
    }

    public function test_kante02_404_ist_abwesenheit_kein_fehler(): void
    {
        Http::fake(['*' => Http::response('Not Found', 404)]);

        $befund = $this->dienst()->ermitteln($this->verbindung());

        $this->assertFalse($befund->liUnterstuetzt);
        $this->assertFalse($befund->svUnterstuetzt);
        $this->assertStringContainsString('404', (string) $befund->hinweis);
    }

    public function test_kante03_fehlerseite_mit_status_200(): void
    {
        Http::fake(['*' => Http::response('Fehler: Aktion unbekannt. Bitte Support kontaktieren.', 200)]);

        $befund = $this->dienst()->ermitteln($this->verbindung());

        $this->assertFalse($befund->liUnterstuetzt);
        $this->assertNotNull($befund->hinweis);
    }

    public function test_kante04_bom_und_latin1_werden_gelesen(): void
    {
        $mitBom = "\xEF\xBB\xBF" . self::LI_XML;
        $latin1 = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n" . self::LI_XML;

        foreach ([$mitBom, $latin1] as $antwort) {
            Http::clearResolvedInstances();
            Http::fake(['*' => Http::response($antwort, 200)]);

            $werte = $this->dienst()->loginInfo($this->verbindung([
                'supplier_key' => 'ids-testshop-' . md5($antwort),
            ]));

            $this->assertNotNull($werte);
            $this->assertFalse($werte['kundennummer']);
            $this->assertTrue($werte['benutzername']);
            $this->assertTrue($werte['passwort']);
        }
    }

    public function test_kante05_fehlendes_element_laesst_die_anderen_gelten(): void
    {
        $xml = <<<'XML'
<Logininformationen>
    <Benutzername_erforderlich>true</Benutzername_erforderlich>
    <Passwort_erforderlich>true</Passwort_erforderlich>
</Logininformationen>
XML;
        Http::fake(['*' => Http::response($xml, 200)]);

        $werte = $this->dienst()->loginInfo($this->verbindung());

        $this->assertNull($werte['kundennummer']);
        $this->assertTrue($werte['benutzername']);
        $this->assertTrue($werte['passwort']);
    }

    public function test_kante06_nicht_normkonforme_werte_werden_nicht_geraten(): void
    {
        $xml = <<<'XML'
<Logininformationen>
    <Kundennummer_erforderlich>1</Kundennummer_erforderlich>
    <Benutzername_erforderlich>0</Benutzername_erforderlich>
    <Passwort_erforderlich>ja</Passwort_erforderlich>
</Logininformationen>
XML;
        Http::fake(['*' => Http::response($xml, 200)]);

        $werte = $this->dienst()->loginInfo($this->verbindung());

        $this->assertNull($werte['kundennummer']);
        $this->assertNull($werte['benutzername']);
        $this->assertNull($werte['passwort']);
        $this->assertStringContainsString('nicht normkonform', (string) $werte['hinweis']);
    }

    public function test_kante07_leere_versionsliste_ist_gueltig(): void
    {
        Http::fake(['*' => Http::response('<Schnittstellenversionen></Schnittstellenversionen>', 200)]);

        $werte = $this->dienst()->versionen($this->verbindung());

        $this->assertTrue($werte['unterstuetzt']);
        $this->assertSame([], $werte['versionen']);
    }

    public function test_kante08_unbekannte_version_wird_aufgenommen_und_gekennzeichnet(): void
    {
        $xml = <<<'XML'
<Schnittstellenversionen>
    <Version>2.5</Version>
    <Version>3.0</Version>
</Schnittstellenversionen>
XML;
        Http::fake(['*' => Http::response($xml, 200)]);

        $werte = $this->dienst()->versionen($this->verbindung());

        $this->assertSame(['2.5', '3.0'], $werte['versionen']);
        $this->assertSame(['3.0'], $werte['unbekannt']);
        $this->assertStringContainsString('3.0', (string) $werte['hinweis']);
    }

    public function test_kante09_timeout_ergibt_leeren_befund_und_laesst_status_unveraendert(): void
    {
        Http::fake(function () {
            throw new ConnectionException('cURL error 28: Operation timed out');
        });

        $verbindung = $this->verbindung(['last_test_status' => 'ok']);

        $befund = $this->dienst()->ermitteln($verbindung);

        $this->assertFalse($befund->liUnterstuetzt);
        $this->assertFalse($befund->svUnterstuetzt);
        $this->assertSame([], $befund->versionen);
        $this->assertSame('ok', $verbindung->refresh()->last_test_status);
    }

    public function test_kante10_redirect_auf_login_seite(): void
    {
        Http::fake(['*' => Http::response('', 302, ['Location' => 'https://shop.example/login'])]);

        $befund = $this->dienst()->ermitteln($this->verbindung());

        $this->assertFalse($befund->liUnterstuetzt);
        $this->assertStringContainsString('leitet um', (string) $befund->hinweis);
    }

    public function test_kante11_uebergrosse_antwort_wird_abgebrochen(): void
    {
        Http::fake(['*' => Http::response('zu gross', 200, ['Content-Length' => '2000000'])]);

        $befund = $this->dienst()->ermitteln($this->verbindung());

        $this->assertFalse($befund->liUnterstuetzt);
        $this->assertStringContainsString('abgebrochen', (string) $befund->hinweis);
    }

    public function test_kante12_inaktive_verbindung_wird_nicht_angefragt(): void
    {
        Http::fake();

        $verbindung = $this->verbindung(['is_active' => false]);

        $befund = $this->dienst()->ermitteln($verbindung);

        Http::assertNothingSent();
        $this->assertFalse($befund->liUnterstuetzt);
        $this->assertNull($verbindung->refresh()->capabilities_checked_at);
    }

    public function test_kante13_fremder_connector_typ_wird_nicht_angefragt(): void
    {
        Http::fake();

        foreach (['omd', 'datanorm'] as $typ) {
            $verbindung = $this->verbindung([
                'supplier_key' => 'shop-' . $typ,
                'connector_type' => $typ,
            ]);

            $befund = $this->dienst()->ermitteln($verbindung);

            $this->assertFalse($befund->liUnterstuetzt);
            $this->assertNull($verbindung->refresh()->capabilities_checked_at);
        }

        Http::assertNothingSent();
    }

    public function test_kante14_parallele_abfrage_wird_serialisiert(): void
    {
        Http::fake();

        $verbindung = $this->verbindung();

        // Erste "Instanz" haelt die Sperre — die zweite darf nicht anfragen.
        $sperre = Cache::lock('ids-capabilities-' . $verbindung->id, 60);
        $this->assertTrue($sperre->get());

        try {
            $befund = $this->dienst()->ermitteln($verbindung);

            Http::assertNothingSent();
            $this->assertFalse($befund->liUnterstuetzt);
            $this->assertStringContainsString('laeuft bereits', (string) $befund->hinweis);
        } finally {
            $sperre->release();
        }
    }

    public function test_kante15_zugangsdaten_erscheinen_nie_im_log(): void
    {
        $logzeilen = [];
        Log::listen(function ($ereignis) use (&$logzeilen) {
            $logzeilen[] = $ereignis->message . ' ' . json_encode($ereignis->context);
        });

        // Fehlerfall (Timeout) UND Normalfall durchlaufen.
        Http::fake(function () {
            throw new ConnectionException('cURL error 28: Operation timed out');
        });
        $this->dienst()->ermitteln($this->verbindung());

        Http::clearResolvedInstances();
        $this->fakeMitNormAntworten();
        $this->dienst()->ermitteln($this->verbindung(['supplier_key' => 'ids-testshop-2']));

        $gesamt = implode("\n", $logzeilen);
        $this->assertStringNotContainsString(self::PASSWORT_FIXTURE, $gesamt);
    }

    // ── K-06: der Schalter traegt ────────────────────────────────────────────

    public function test_k06_schalter_aus_verhaelt_sich_wie_vorher(): void
    {
        config()->set('ids.capabilities.aktiv', false);
        Http::fake(['*' => Http::response('OK', 200)]);

        $verbindung = $this->verbindung();

        app(SupplierConnectionTestService::class)->test($verbindung);

        Http::assertNotSent(function (Request $request) {
            $action = self::queryVon($request)['action'] ?? null;

            return in_array($action, ['LI', 'SV'], true);
        });
        $this->assertNull($verbindung->refresh()->capabilities_checked_at);
    }

    public function test_verbindungspruefung_fragt_faehigkeiten_vor_dem_suchtest_ab(): void
    {
        $this->fakeMitNormAntworten();

        $verbindung = $this->verbindung();

        app(SupplierConnectionTestService::class)->test($verbindung);

        Http::assertSent(function (Request $request) {
            return (self::queryVon($request)['action'] ?? null) === 'LI';
        });
        $this->assertNotNull($verbindung->refresh()->capabilities_checked_at);
    }
}
