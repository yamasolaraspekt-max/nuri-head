<?php

namespace Tests\Feature\Styleguide;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * PB-023 — die Hausplaner-Insel liegt auf der Referenzflaeche, und die Bruecke dorthin haelt.
 *
 * -------------------------------------------------------------------------------------------
 * DIE MUTATIONSPROBE VOR DIESER DATEI — 8 Mutationen, ACHT kamen durch
 *
 *   1 Klassenname mit Tippfehler (hp-ok-pille -> hp-ok-pile)      BLIND
 *   2 die Stilschicht wird gar nicht geladen                      BLIND
 *   3 Buehne fort - die Sperre deckt die ganze Seite zu           BLIND
 *   4 zwei Pillen-Zustaende vertauscht                            BLIND
 *   5 A11y-Symbol der Schwere entfernt                            BLIND
 *   6 eine ganze Familie faellt raus (hp-fn-)                     BLIND
 *   7 Regel in der Insel umbenannt (Klasse verwaist)              BLIND
 *   8 Wegweiser-Pfeil verliert seine Klasse                       BLIND
 *
 * Gemessen gegen den vollen vorhandenen Stand (Insel-Suite 1583/1583 gruen); Wiederherstellung
 * md5-identisch. ACHT VON ACHT ist kein Zufall dieser Datei: vor ihr gab es ueber den Styleguide
 * ueberhaupt keine Zusage, weder in `tests/` noch in der Insel-Suite.
 *
 * Das ist dieselbe Lage wie F-15 (`docs/auftraege/FEHLERKLASSEN.md`) — *"die Wahrheit wandert in
 * eine zweite Datei, und zwischen beiden liegt nichts"*. Hier sind es sogar drei Dateien: das
 * Blade nennt eine Klasse, die Quell-Stilschicht definiert sie, und das GEBAUTE Artefakt ist das,
 * was der Browser laedt. Faellt eine der drei aus dem Tritt, zeigt die Referenzflaeche ungestyltes
 * Markup — **und ungestyltes Markup sieht im Screenshot-Diff aus wie eine Komponente, die es gibt.**
 *
 * -------------------------------------------------------------------------------------------
 * WARUM HIER KOMMENTARFREI GELESEN WIRD
 *
 * Der Erklaerblock im Blade nennt `hp-ok-`, `hp-ep-`, `hp-ef-`, `hp-wg-`, `hp-schiene-`, `hp-mb-`
 * im Fliesstext, und der CSS-Kommentar zur Buehne nennt `.hp-mb-flaeche`. Eine Zusage, die die
 * Datei roh liest, zaehlte diese Erwaehnungen als Verwendung mit und bliebe gruen, nachdem das
 * letzte Markup entfernt wurde.
 *
 * **Das ist F-09** (`docs/auftraege/FEHLERKLASSEN.md`) — der Fehler, den ich achtmal selbst
 * gemacht habe. Diesmal vor dem Ausloesen bemerkt, weil der Kommentar zuerst geschrieben war.
 */
class HausplanerInselTest extends TestCase
{
    use RefreshDatabase;

    private const BLADE = 'resources/views/admin/styleguide/index.blade.php';
    private const QUELLE = 'resources/planner/hausplaner/hausplaner.css';
    private const GEBAUT = 'public/hausplaner/hausplaner.css';

    /** Die Familien, die auf der Referenzflaeche liegen MUESSEN — aufgezaehlt, nicht erraten. */
    private const FAMILIEN = ['hp-ok-', 'hp-ep-', 'hp-ef-', 'hp-gz-', 'hp-wg-', 'hp-schiene-', 'hp-fn-', 'hp-mb-'];

    private function datei(string $pfad): string
    {
        return file_get_contents(base_path($pfad));
    }

    /** Blade-, HTML- und CSS-Kommentare fort — sonst haelt eine Zusage die Erklaerung fuer Markup. */
    private function ohneKommentare(string $s): string
    {
        return preg_replace(['/\{\{--[\s\S]*?--\}\}/', '/<!--[\s\S]*?-->/', '/\/\*[\s\S]*?\*\//'], '', $s);
    }

    /** Die `hp-`-Klassen, die das Blade wirklich an ein Element schreibt. */
    private function benutzteKlassen(): array
    {
        $raus = [];
        preg_match_all('/class="([^"]+)"/', $this->ohneKommentare($this->datei(self::BLADE)), $treffer);
        foreach ($treffer[1] as $liste) {
            foreach (preg_split('/\s+/', $liste) as $k) {
                if (str_starts_with($k, 'hp-')) {
                    $raus[$k] = true;
                }
            }
        }
        ksort($raus);

        return array_keys($raus);
    }

    /** Traegt eine Stilschicht eine Regel fuer genau diese Klasse? Auf Wortgrenze, nicht per `strpos`. */
    private function hatRegel(string $css, string $klasse): bool
    {
        return preg_match('/\.'.preg_quote($klasse, '/').'\s*[,{]/', $css) === 1;
    }

    // --- Die Stilschicht kommt ueberhaupt an -------------------------------------------------------

    /**
     * Mutation 2 kam durch: ohne diesen Verweis traegt die Seite `hp-`-Klassen ohne eine einzige
     * Regel dahinter. **Deshalb wird die Seite hier wirklich gerendert** und nicht nur gelesen —
     * der `@push('style')`-Block steht im Blade auch dann, wenn ihn das Layout nie ausgibt.
     */
    public function test_die_seite_laedt_die_stilschicht_der_insel(): void
    {
        // Das Kennwort wird neu gesetzt: die Factory traegt einen fest verdrahteten Hash mit
        // Kostenfaktor 10, `phpunit.xml` erzwingt aber BCRYPT_ROUNDS=4. Laravel prueft die
        // Konfiguration beim Lesen und wirft sonst *"Could not verify the hashed value's
        // configuration"* — ein Fehler der Testumgebung, der wie ein Fehler der Seite aussieht.
        // **Dieselbe Falle wie in `SidebarCountTest::konto()`; dort steht sie seit PB-047
        // aufgeschrieben, und ich bin trotzdem hineingelaufen.** Zwei Fundstellen sind der
        // Anlass, daraus einen gemeinsamen Helfer zu machen — das ist ein eigener Posten.
        /** @var User $nutzer */
        $nutzer = User::factory()->create(['password' => Hash::make('probe')]);

        $antwort = $this->actingAs($nutzer)->get('/admin/styleguide');

        $antwort->assertOk();

        // **Geprueft wird das VERWEIS-ELEMENT, nicht der Dateiname.** Meine erste Fassung suchte
        // nur `hausplaner/hausplaner.css` — und blieb gruen, nachdem der `<link>` entfernt war:
        // die Ueberschrift von Abschnitt 9 nennt denselben Pfad als sichtbaren Text. Die Zusage
        // traf ein Muster, nicht die Sache. *Genau daran ist sie in der Gegenprobe aufgefallen.*
        $this->assertMatchesRegularExpression(
            '/<link[^>]+href="[^"]*hausplaner\/hausplaner\.css"/',
            $antwort->getContent(),
            'die Seite bindet die Stilschicht der Insel nicht mehr ein — alle hp--Klassen bleiben ohne Regel'
        );
        // presence-Partner nach R2: die Seite hat ueberhaupt Inhalt, sonst misst die Zusage Leere.
        $antwort->assertSee('9 · Hausplaner-Insel', false);
    }

    // --- Die Bruecke: benutzt <=> definiert --------------------------------------------------------

    /**
     * Mutationen 1 und 7 kamen durch — von beiden Seiten dieselbe Wirkung: ein Element ohne Regel.
     * **Geprueft wird gegen BEIDE Artefakte.** Die Quelle ist, was gepflegt wird; das gebaute
     * Artefakt ist, was der Browser laedt. Nur die Quelle zu pruefen hiesse, einen veralteten Bau
     * fuer gestylt zu halten.
     */
    public function test_jede_benutzte_insel_klasse_hat_eine_regel_in_quelle_und_bau(): void
    {
        $benutzt = $this->benutzteKlassen();
        $this->assertGreaterThanOrEqual(
            30,
            count($benutzt),
            'zu wenige Insel-Klassen auf der Flaeche — die Zusage misst Leere'
        );

        $quelle = $this->datei(self::QUELLE);
        $this->assertFileExists(base_path(self::GEBAUT), 'das gebaute Artefakt fehlt — dann greift der Verweis im Blade nicht');
        $gebaut = $this->datei(self::GEBAUT);

        foreach ($benutzt as $k) {
            $this->assertTrue(
                $this->hatRegel($quelle, $k),
                "`.{$k}` steht auf der Referenzflaeche, aber in keiner Regel der Quell-Stilschicht — das Element bleibt ungestylt"
            );
            $this->assertTrue(
                $this->hatRegel($gebaut, $k),
                "`.{$k}` fehlt im GEBAUTEN Artefakt — die Quelle wurde geaendert, ohne neu zu bauen; im Browser ist die Klasse wirkungslos"
            );
        }
    }

    // --- Die Familien ------------------------------------------------------------------------------

    /**
     * Mutation 6 kam durch: eine ganze Familie fiel aus der Flaeche, und nichts sagte es.
     * Die Zahl allein reichte nicht — bei acht Familien darf eine verschwinden und die Schwelle
     * von sechs haelt trotzdem. **Deshalb die aufgezaehlte Liste zusaetzlich zur Zahl.**
     */
    public function test_die_flaeche_zeigt_die_benannten_familien(): void
    {
        $blade = $this->ohneKommentare($this->datei(self::BLADE));

        $gefunden = [];
        preg_match_all('/class="([^"]+)"/', $blade, $treffer);
        foreach ($treffer[1] as $liste) {
            foreach (preg_split('/\s+/', $liste) as $k) {
                if (preg_match('/^(hp-[a-z0-9]+-)/', $k, $m)) {
                    $gefunden[$m[1]] = true;
                }
            }
        }

        $this->assertGreaterThanOrEqual(6, count($gefunden), 'weniger als sechs Insel-Familien auf der Referenzflaeche (K-04)');
        foreach (self::FAMILIEN as $f) {
            $this->assertArrayHasKey($f, $gefunden, "die Familie `{$f}` ist von der Referenzflaeche verschwunden");
        }
    }

    // --- Die Zustaende sitzen an ihrer Beschriftung ------------------------------------------------

    /**
     * Mutation 4 kam durch: die Pille „aktuell" trug den Zustand „veraltet". Die drei Zustaende
     * unterscheiden sich nur in der Farbe — vertauscht sieht die Flaeche vollstaendig aus und
     * zeigt die falsche Zuordnung. **Genau das soll eine Referenzflaeche verhindern.**
     */
    public function test_die_drei_zustaende_des_objektkopfs_tragen_ihre_eigene_beschriftung(): void
    {
        $blade = $this->ohneKommentare($this->datei(self::BLADE));

        foreach ([['aktuell', 'aktuell'], ['veraltet', 'veraltet'], ['nie', 'nie übernommen']] as [$zustand, $text]) {
            $this->assertMatchesRegularExpression(
                '/hp-ok-pille--'.$zustand.'">'.preg_quote($text, '/').'</u',
                $blade,
                "die Pille `--{$zustand}` traegt nicht mehr die Beschriftung >>{$text}<< — Zustand und Text sind vertauscht"
            );
        }
    }

    // --- Was nur einmal da ist und leicht verschwindet ---------------------------------------------

    /**
     * Mutationen 5 und 8 kamen durch. Beides sind Elemente, die man beim Kuerzen zuerst wegnimmt,
     * weil sie „nur" ein Zeichen tragen — und beide tragen eine Aussage:
     *
     *   `hp-ep-schwere-symbol`   die Schwere steht als Symbol UND Text da, nicht nur als Farbe
     *   `hp-gz-wegweiser-pfeil`  ohne `flex: 0 0 auto` schrumpft der Pfeil und der Satz rutscht
     */
    public function test_symbol_und_pfeil_bleiben_mit_ihrer_klasse_stehen(): void
    {
        $blade = $this->ohneKommentare($this->datei(self::BLADE));

        $this->assertStringContainsString(
            'aria-hidden class="hp-ep-schwere-symbol"',
            $blade,
            'die Schwere wird nur noch ueber die Farbe getragen — auf der Flaeche, die A11y vorfuehren soll'
        );
        $this->assertStringContainsString(
            'aria-hidden class="hp-gz-wegweiser-pfeil"',
            $blade,
            'der Wegweiser-Pfeil hat seine Klasse verloren'
        );
        // presence-Partner: der Satz, zu dem der Pfeil gehoert, steht auch noch da.
        $this->assertStringContainsString('hp-gz-wegweiser-satz', $blade, 'der Wegweiser hat seinen Satz verloren');
    }

    // --- Der ehrliche Vermerk, und wann er wieder verschwinden muss --------------------------------

    /**
     * **IM BROWSER GEMESSEN, nicht angenommen** (Chrome 1440 px, `setCacheEnabled(false)`,
     * angemeldet):
     *
     *   Insel-Stilschicht geladen     JA
     *   Familien im Baum              8   hp-ef- hp-ep- hp-fn- hp-gz- hp-mb- hp-ok- hp-schiene- hp-wg-
     *   Sperre bleibt in der Buehne   true  (248 px hoch, Seite 1000 px)
     *   .hp-ok-pille--aktuell         rgba(0, 0, 0, 0)   <- durchsichtig
     *   .hp-ep-befund                 rgba(0, 0, 0, 0)   <- durchsichtig
     *   --hp-ok-soft / --hp-err-soft  NICHT DEFINIERT
     *
     * **Die Ursache steht im Code, nicht im Blatt.** Die Stilschicht fuehrt in ihrem `:root` genau
     * eine Zeile — `--hp-stilschicht: 1`. Alle Farbtokens setzt `setzeTokenVariablen()` zur
     * LAUFZEIT, und zwar in `main.tsx` erst, nachdem `#hausplaner-root` UND `#hausplaner-scene`
     * gefunden wurden. Auf dieser Seite laeuft das Buendel nicht — also loest jedes
     * `var(--hp-…)` ins Leere auf.
     *
     * **Warum die Tokens hier nicht von Hand nachgetragen werden:** `studioDaten.ts` ist die eine
     * Wahrheit ueber diese Farben. Eine abgeschriebene Liste im Blade waere die zweite und altert
     * getrennt — genau das, was `PB-023+024` in K-02 ausdruecklich verbietet.
     *
     * **Diese Zusage retiriert sich selbst.** Traegt die Stilschicht die Tokens eines Tages
     * wirklich (PB-024-N2), muss der Vermerk WEG — sonst behauptet die Flaeche einen Mangel, den
     * es nicht mehr gibt. Beide Richtungen stehen hier.
     */
    public function test_der_vermerk_zur_fehlenden_farbe_steht_genau_solange_er_stimmt(): void
    {
        $quelle = $this->datei(self::QUELLE);
        $blade = $this->ohneKommentare($this->datei(self::BLADE));

        // Fuehrt die Stilschicht die Farbtokens selbst? Geprueft an einem, den es geben muesste.
        $traegtTokens = preg_match('/:root\s*\{[^}]*--hp-accent\b/', $quelle) === 1;

        if ($traegtTokens) {
            $this->assertStringNotContainsString(
                'zeigen Struktur, noch nicht Farbe',
                $blade,
                'die Stilschicht fuehrt die Tokens inzwischen selbst — der Vermerk behauptet einen Mangel, den es nicht mehr gibt'
            );

            return;
        }

        $this->assertStringContainsString(
            'zeigen Struktur, noch nicht Farbe',
            $blade,
            'die Flaeche zeigt die Insel farblos und sagt es nicht — dann beglaubigt der Screenshot-Diff einen falschen Zustand'
        );
        $this->assertStringContainsString('setzeTokenVariablen()', $blade, 'der Vermerk nennt die Ursache nicht');
        $this->assertStringContainsString('PB-024-N2', $blade, 'der Vermerk nennt den Weg dorthin nicht');
    }

    // --- Die Grenze: was in der Insel absolut liegt, bleibt hier in seiner Buehne ------------------

    /**
     * Mutation 3 kam durch, und sie ist die unangenehmste. `.hp-mb-flaeche` traegt
     * `position: absolute; inset: 0`. Ohne einen Vorfahren mit `position: relative` verankert sie
     * sich am naechsten positionierten Element — im Zweifel an der Seite — **und legt sich ueber
     * den ganzen Styleguide.** Die Referenzflaeche zerstoerte dann das, wofuer es sie gibt.
     */
    public function test_absolut_liegende_insel_flaechen_stehen_in_einer_begrenzten_buehne(): void
    {
        $blade = $this->ohneKommentare($this->datei(self::BLADE));

        $this->assertStringContainsString('.sg-hp-buehne { position: relative;', $blade, 'die Buehne ist nicht mehr positioniert');

        $buehne = strpos($blade, 'class="sg-hp-buehne"');
        $flaeche = strpos($blade, 'class="hp-mb-flaeche"');
        $this->assertNotFalse($buehne, 'die begrenzte Buehne fehlt');
        $this->assertNotFalse($flaeche, 'die Mindestbreiten-Flaeche fehlt — die Zusage misst Leere');
        $this->assertLessThan(
            $flaeche,
            $buehne,
            'die absolut liegende Flaeche steht nicht mehr in der Buehne — sie legt sich ueber den Styleguide'
        );
    }
}
