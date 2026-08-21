<?php

namespace Tests\Feature\Security;

use App\Models\ImportedIdsItem;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Z2-W0-11 **Teil A** — Zuschreibung des IDS-Imports und die toten CSRF-Ausnahmen.
 *
 * **A1, und die Messung faellt anders aus als der Auftrag annimmt.** `IdsController::callback()`
 * nahm den Importeur aus der Query (`?uid=…`). Das Auftragsblatt schliesst daraus auf eine
 * Fremdzuschreibung im Bestand. Nachgemessen trifft das **nicht** zu: `imported_ids_items` hat
 * **keine Spalte `user_id`** — in keiner der beiden Migrationen —, und `user_id` steht auch nicht
 * im `$fillable` des Modells. `create(['user_id' => …])` wirft den Wert also zweifach weg.
 * Der Import wird angelegt, aber er gehoert niemandem.
 *
 * Der Wert landete an genau EINER Stelle: in `Log::info("IDS meta", ['user_id' => …])`.
 * Das ist kein harmloser Rest, sondern die unangenehmere Variante — die einzige Aufzeichnung,
 * die eine Zuschreibung behauptet, trug einen Wert, den der Aufrufer frei bestimmte. Wer nach
 * einem unerwuenschten Import sucht, liest dort den Namen, den der Ausloeser hineinschrieb.
 * Ein Protokoll, das falsch zuschreibt, ist schlechter als eines, das schweigt.
 *
 * Was Teil A deshalb wirklich leistet: die Zuschreibung im Protokoll wird ehrlich. Was Teil A
 * NICHT leistet und auch nicht leisten darf: eine echte Zuschreibung im Bestand — dafuer braeuchte
 * es eine neue Spalte, und Schemaaenderungen sind ein Gate. Der Befund ist gemeldet, nicht gebaut.
 *
 * **A2:** fuenf Eintraege in `VerifyCsrfToken::$except` trafen keine Route. Eine Ausnahme, die
 * nichts trifft, schuetzt niemanden — sie verdeckt nur, welche Schreibpfade wirklich ohne CSRF
 * laufen. Die Ratsche unten laesst kuenftig keine neue ins Haus.
 *
 * **NICHT hier:** Teil B (signierter State fuer den echten IDS-Rueckweg) haengt an Y-12 —
 * wie der externe Shop zurueckliefert, ist im Haus nicht messbar. Die Kriterien A und B der
 * Nachvollzugs-Matrix des Auftrags gehoeren dorthin. Deshalb bleibt die Ausnahme fuer
 * `ids/callback` vorerst stehen.
 *
 * Laeuft ausschliesslich gegen `ticket_testing` (`phpunit.xml` erzwingt `DB_DATABASE`).
 */
class IdsCallbackZuschreibungTest extends TestCase
{
    use DatabaseTransactions;

    private function nutzer(): User
    {
        return User::factory()->create([
            'password' => 'password', 'name' => (string) random_int(1, 9999), 'is_admin' => 0,
        ]);
    }

    private function warenkorb(string $artikelNr): string
    {
        return <<<XML
        <?xml version="1.0" encoding="UTF-8"?>
        <IDS>
          <WarenkorbInfo><Date>20260821</Date><Time>2200</Time></WarenkorbInfo>
          <Order>
            <OrderItem>
              <ArtNo>{$artikelNr}</ArtNo><Kurztext>Pruefartikel</Kurztext>
              <Qty>2</Qty><QU>Stk</QU>
              <OfferPrice>10.00</OfferPrice><NetPrice>8.00</NetPrice><VAT>19</VAT>
            </OrderItem>
          </Order>
        </IDS>
        XML;
    }

    /**
     * Kriterium C, der Kern — gemessen an der einzigen Stelle, an der der Wert ankommt.
     *
     * Angemeldet ist A, in der Query steht B. Der Controller muss A fuehren. Weil die Tabelle
     * keine `user_id` traegt (siehe Zusage darunter), ist das Protokoll der Messpunkt — nicht
     * ersatzweise, sondern weil dort der Schaden lag.
     */
    public function test_uid_aus_der_query_bestimmt_die_zuschreibung_nicht_mehr(): void
    {
        Event::fake();
        Log::spy();

        $angemeldet = $this->nutzer();
        $fremd = $this->nutzer();
        $artikelNr = 'W011A-' . random_int(100000, 999999);

        $this->actingAs($angemeldet)
            ->post('/ids/callback?uid=' . $fremd->id . '&auto=0', [
                'warenkorb' => $this->warenkorb($artikelNr),
            ])
            ->assertOk();

        $this->assertSame(
            1,
            ImportedIdsItem::where('article_no', $artikelNr)->count(),
            'Der Import muss stattgefunden haben — sonst misst die Zusage nichts.',
        );

        Log::shouldHaveReceived('info')
            ->withArgs(function ($nachricht, $zusatz = []) use ($angemeldet, $fremd) {
                if (!str_contains((string) $nachricht, 'IDS meta')) {
                    return false;
                }
                // Beide Seiten pruefen: "ist der Angemeldete" allein waere gruen, wenn dort
                // gar nichts mehr stuende. Gemessen wird der Wert, nicht seine Abwesenheit.
                return (int) ($zusatz['user_id'] ?? 0) === (int) $angemeldet->id
                    && (int) ($zusatz['user_id'] ?? 0) !== (int) $fremd->id;
            })
            ->once();
    }

    /**
     * Kriterium C, Gegenprobe — ohne `uid` in der Query aendert sich nichts.
     * Die Sitzung ist die Quelle, nicht ein Rueckfall fuer den Fall fehlender Query.
     */
    public function test_ohne_uid_in_der_query_fuehrt_der_controller_den_angemeldeten(): void
    {
        Event::fake();
        Log::spy();

        $angemeldet = $this->nutzer();

        $this->actingAs($angemeldet)
            ->post('/ids/callback', ['warenkorb' => $this->warenkorb('W011B-' . random_int(100000, 999999))])
            ->assertOk();

        Log::shouldHaveReceived('info')
            ->withArgs(fn ($nachricht, $zusatz = []) => str_contains((string) $nachricht, 'IDS meta')
                && (int) ($zusatz['user_id'] ?? 0) === (int) $angemeldet->id)
            ->once();
    }

    /**
     * CHARAKTERISIERUNG, kein Soll — der Befund selbst, festgehalten wo er nicht verloren geht.
     *
     * `imported_ids_items` traegt keine `user_id`; der IDS-Import hat im Bestand keinen Urheber.
     * Das ist eine Luecke, aber ihre Behebung ist eine Schemaaenderung und damit ein Gate.
     * Diese Zusage haelt den gemessenen Zustand fest und wird rot, sobald jemand die Spalte
     * anlegt — dann ist die Zuschreibung zu Ende zu bauen, und `$fillable` muss mit.
     * Wer sie dann nur "gruen macht", macht aus einem gemeldeten Befund einen verschwiegenen.
     */
    public function test_charakterisierung_der_import_hat_im_bestand_keinen_urheber(): void
    {
        $this->assertFalse(
            \Illuminate\Support\Facades\Schema::hasColumn('imported_ids_items', 'user_id'),
            'Es gibt jetzt eine user_id-Spalte — dann muss die Zuschreibung wirklich gespeichert '
                . 'werden (Modell-$fillable ergaenzen) und diese Charakterisierung durch eine echte '
                . 'Zusage ersetzt werden. Siehe Z2-W0-11 Teil A, Befund an den Planner.',
        );

        $this->assertNotContains(
            'user_id',
            (new ImportedIdsItem())->getFillable(),
            'Solange es die Spalte nicht gibt, darf user_id auch nicht im $fillable stehen — '
                . 'sonst schlaegt der Anlegevorgang fehl statt den Wert stillschweigend zu verwerfen.',
        );
    }

    /**
     * Kriterium D — jede CSRF-Ausnahme muss eine reale Route treffen.
     *
     * Das ist die Ratsche: sie haelt nicht nur die fuenf entfernten Eintraege draussen,
     * sondern jede kuenftige Ausnahme, die ins Leere zeigt — auch die mit fuehrendem
     * Schraegstrich, an der die alte Dublette scheiterte.
     */
    public function test_jede_csrf_ausnahme_trifft_eine_reale_route(): void
    {
        $ausnahmen = (new \App\Http\Middleware\VerifyCsrfToken(app(), app('encrypter')))
            ->getExcludedPaths();

        $this->assertNotEmpty($ausnahmen, 'Ohne Eintraege misst diese Zusage nichts.');

        $routen = collect(Route::getRoutes())->map(fn ($r) => trim($r->uri(), '/'))->unique()->all();

        $tot = [];
        foreach ($ausnahmen as $muster) {
            $normiert = trim((string) $muster, '/');
            $trifft = false;
            foreach ($routen as $uri) {
                // Der Stern der Ausnahme steht fuer ein Wegstueck; die Route schreibt dort
                // einen benannten Platzhalter. Beide auf dieselbe Form bringen, dann vergleichen.
                if (Str::is($normiert, $uri) || Str::is($normiert, preg_replace('#\{[^}]+\}#', '*', $uri))) {
                    $trifft = true;
                    break;
                }
            }
            if (!$trifft) {
                $tot[] = $muster;
            }
        }

        $this->assertSame(
            [],
            $tot,
            "Diese CSRF-Ausnahmen treffen keine registrierte Route und schuetzen niemanden: \n  "
                . implode("\n  ", $tot),
        );
    }

    /** Kriterium D, namentlich — die fuenf gemessenen Leichen bleiben draussen. */
    public function test_die_fuenf_gemessenen_toten_ausnahmen_sind_fort(): void
    {
        $ausnahmen = (new \App\Http\Middleware\VerifyCsrfToken(app(), app('encrypter')))
            ->getExcludedPaths();

        foreach ([
            'api/reminder/*/status',
            'api/due-personal-notes',
            'ids/search/callback',
            '/ids/receive',
            '/ids/callback',
        ] as $tot) {
            $this->assertNotContains($tot, $ausnahmen, "Tote Ausnahme wieder eingetragen: {$tot}");
        }

        // Und die, die wirkt, ist noch da — Teil B haengt an Y-12, bis dahin bleibt sie.
        $this->assertContains('ids/callback', $ausnahmen);
    }
}
