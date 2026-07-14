<?php

namespace Tests\Unit\Auslegung;

use App\Repositories\CatalogDeviceRepository;
use App\Services\Auslegung\WpAuslegungsEingabe;
use App\Services\Auslegung\WpAuslegungsketteService;
use App\Services\Energie\Contracts\WpKennlinie;
use App\Services\Heizlast\BivalenzService;
use App\Services\Heizlast\WaermepumpenMatchService;
use Illuminate\Support\Collection;
use Tests\TestCase;

/**
 * WP Stufe 3 — Orchestrator-Logik (Gate A, Ranking E-WP4, Label, quelle_fehlt, Config-Kipptest,
 * G-Gates, Determinismus). Kern-Services werden GESTUBT — getestet wird die Verdrahtungs-/Ranking-
 * Logik des Orchestrators, nicht der (isolierte) BivalenzService-Kern.
 */
class WpAuslegungsketteServiceTest extends TestCase
{
    /** @param array<int,array<string,mixed>> $kandidaten @param array<int,array<string,mixed>> $metrics */
    private function orchestrator(array $kandidaten, array $metrics): WpAuslegungsketteService
    {
        $match = new class($kandidaten) extends WaermepumpenMatchService
        {
            public function __construct(private array $stub) {}

            public function kandidaten(float $benoetigtKw, string $wpTyp, string $heizsystem, float $vorlaufC, int $limit = 6, bool $belastbar = true): array
            {
                return ['geraete' => $this->stub, 'design_punkt' => 'A-7', 'hinweis' => $this->stub === [] ? 'leer' : null, 'verbindlich' => true];
            }
        };

        $biv = new class($metrics) extends BivalenzService
        {
            public function __construct(private array $metrics) {}

            public function berechne(WpKennlinie $wp, float $phiHlKw, float $qHeizKwh, float $qWwKwh, bool $wwMitWp, float $vorlaufC, ?string $plz, float $raumtempC = 20.0, ?float $lat = null, ?float $lon = null): array
            {
                return $this->metrics[$wp->id] ?? [];
            }
        };

        $ids = array_map(fn ($k) => $k['id'], $kandidaten);
        $repo = new class($ids) extends CatalogDeviceRepository
        {
            public function __construct(private array $ids) {}

            public function heatPumps(): Collection
            {
                return collect($this->ids)->map(fn ($id) => new class($id) implements WpKennlinie
                {
                    public function __construct(public int $id) {}
                });
            }
        };

        return new WpAuslegungsketteService($match, $biv, $repo);
    }

    private function eingabe(array $over = []): WpAuslegungsEingabe
    {
        // array_key_exists (nicht ??): explizites null muss durchkommen (Gate-Tests).
        $v = fn (string $k, $default) => array_key_exists($k, $over) ? $over[$k] : $default;

        return new WpAuslegungsEingabe(
            phiHlKw: $v('phiHlKw', 8.0),
            qHeizKwh: $v('qHeizKwh', 12000.0),
            qWwKwh: $v('qWwKwh', 2000.0),
            wwMitWp: $v('wwMitWp', true),
            vorlaufC: $v('vorlaufC', 50.0),
            plz: $v('plz', '01067'),
            wpTyp: $v('wpTyp', 'luft_wasser'),
            heizsystem: $v('heizsystem', 'heizkoerper'),
        );
    }

    /** Standard-Testfall: 3 Geräte, id3 hat höchste JAZ aber Vorlauf-Grenze zu niedrig. */
    private function standard(): WpAuslegungsketteService
    {
        return $this->orchestrator(
            [
                ['id' => 1, 'hersteller' => 'NIBE', 'modell' => 'S1155', 'max_vorlauf_c' => 60],
                ['id' => 2, 'hersteller' => 'Buderus', 'modell' => 'WLW', 'max_vorlauf_c' => 55],
                ['id' => 3, 'hersteller' => 'Viessmann', 'modell' => 'Vitocal', 'max_vorlauf_c' => 45],
            ],
            [
                1 => ['jaz' => 4.5, 'deckung_ne_pct' => 95, 'bivalenzpunkt_c' => -4, 'estab_waerme_anteil_pct' => 3.0, 'laufstunden_h' => 1800, 'bivalenz_status' => 'ok'],
                2 => ['jaz' => 4.0, 'deckung_ne_pct' => 92, 'bivalenzpunkt_c' => -3, 'estab_waerme_anteil_pct' => 5.0, 'laufstunden_h' => 1900, 'bivalenz_status' => 'ok'],
                3 => ['jaz' => 5.0, 'deckung_ne_pct' => 95, 'bivalenzpunkt_c' => -5, 'estab_waerme_anteil_pct' => 2.0, 'laufstunden_h' => 1700, 'bivalenz_status' => 'ok'],
            ],
        );
    }

    public function test_ranking_reihenfolge_und_gate_a(): void
    {
        $r = $this->standard()->rankeKandidaten($this->eingabe());
        $ids = array_column($r['kandidaten'], 'id');

        // id3 (beste JAZ) ist ungeeignet (Vorlauf 50 > max 45) → ans Ende; id1(4,5) vor id2(4,0).
        $this->assertSame([1, 2, 3], $ids);
        $id3 = collect($r['kandidaten'])->firstWhere('id', 3);
        $this->assertFalse($id3['geeignet']);
        $this->assertSame('vorlauf_ueber_grenze', $id3['ausschluss_grund']);
        $this->assertTrue(collect($r['kandidaten'])->firstWhere('id', 1)['geeignet']);
    }

    public function test_label_und_verbindlich(): void
    {
        $r = $this->standard()->rankeKandidaten($this->eingabe());
        $this->assertSame('informativ, nicht verbindlich', $r['label']);
        $this->assertFalse($r['verbindlich']);
    }

    public function test_nibe_gekennzeichnet_nicht_geboostet(): void
    {
        // NIBE (id1) wird gekennzeichnet ...
        $r = $this->standard()->rankeKandidaten($this->eingabe());
        $this->assertTrue(collect($r['kandidaten'])->firstWhere('id', 1)['nibe']);

        // ... aber NICHT geboostet: hat Buderus die bessere JAZ, rankt Buderus vor NIBE.
        $o = $this->orchestrator(
            [
                ['id' => 1, 'hersteller' => 'NIBE', 'modell' => 'S', 'max_vorlauf_c' => 60],
                ['id' => 2, 'hersteller' => 'Buderus', 'modell' => 'W', 'max_vorlauf_c' => 60],
            ],
            [
                1 => ['jaz' => 4.0, 'deckung_ne_pct' => 95],
                2 => ['jaz' => 5.0, 'deckung_ne_pct' => 95], // Buderus besser
            ],
        );
        $ids = array_column($o->rankeKandidaten($this->eingabe())['kandidaten'], 'id');
        $this->assertSame([2, 1], $ids); // Buderus vor NIBE → kein Marken-Boost
    }

    public function test_invest_und_verfuegbarkeit_quelle_fehlt(): void
    {
        $r = $this->standard()->rankeKandidaten($this->eingabe());
        $k0 = $r['kandidaten'][0];
        $this->assertTrue($k0['invest_quelle_fehlt']);
        $this->assertTrue($k0['verfuegbarkeit_quelle_fehlt']);
        $this->assertEqualsCanonicalizing(['investitionskosten', 'verfuegbarkeit'], $r['gewichte_ausgesetzt']);
        $this->assertStringContainsString('Preisquelle', implode(' ', $r['hinweise']));
    }

    public function test_config_gate_a_ist_load_bearing(): void
    {
        // Default min_deckung 90 → id2 (92) geeignet.
        $this->assertTrue(collect($this->standard()->rankeKandidaten($this->eingabe())['kandidaten'])->firstWhere('id', 2)['geeignet']);

        // Config-Kipp: min_deckung 93 → id2 (92) wird ungeeignet (deckung_zu_gering).
        config(['wp_auslegung.gate_a.min_deckung_ne_pct' => 93]);
        $id2 = collect($this->standard()->rankeKandidaten($this->eingabe())['kandidaten'])->firstWhere('id', 2);
        $this->assertFalse($id2['geeignet']);
        $this->assertSame('deckung_zu_gering', $id2['ausschluss_grund']);
    }

    public function test_gewicht_ist_load_bearing(): void
    {
        // Eingabereihenfolge [id2(jaz4,0), id1(jaz4,5)]: Default (jaz 0,5) sortiert nach JAZ → [1,2].
        $o = $this->orchestrator(
            [
                ['id' => 2, 'hersteller' => 'Buderus', 'modell' => 'W', 'max_vorlauf_c' => 60],
                ['id' => 1, 'hersteller' => 'NIBE', 'modell' => 'S', 'max_vorlauf_c' => 60],
            ],
            [2 => ['jaz' => 4.0, 'deckung_ne_pct' => 95], 1 => ['jaz' => 4.5, 'deckung_ne_pct' => 95]],
        );
        $this->assertSame([1, 2], array_column($o->rankeKandidaten($this->eingabe())['kandidaten'], 'id'));

        // JAZ-Gewicht 0 → Score neutral → Eingabereihenfolge bleibt [2,1]. Gewicht ist load-bearing.
        config(['wp_auslegung.gewichte.jaz' => 0.0]);
        $this->assertSame([2, 1], array_column($o->rankeKandidaten($this->eingabe())['kandidaten'], 'id'));
    }

    public function test_g_gates_fehlende_operanden(): void
    {
        $rVorlauf = $this->standard()->rankeKandidaten($this->eingabe(['vorlaufC' => null]));
        $this->assertFalse($rVorlauf['anwendbar']);
        $this->assertContains('vorlauf_fehlt', $rVorlauf['gates_offen']);
        $this->assertSame([], $rVorlauf['kandidaten']);

        $rWw = $this->standard()->rankeKandidaten($this->eingabe(['wwMitWp' => null]));
        $this->assertContains('ww_entscheidung_fehlt', $rWw['gates_offen']);

        $rTyp = $this->standard()->rankeKandidaten($this->eingabe(['wpTyp' => '']));
        $this->assertContains('geraetetyp_fehlt', $rTyp['gates_offen']);
    }

    public function test_keine_kandidaten_kein_leeres_fertig(): void
    {
        $r = $this->orchestrator([], [])->rankeKandidaten($this->eingabe());
        $this->assertSame([], $r['kandidaten']);
        $this->assertStringContainsString('Keine passenden Geräte', implode(' ', $r['hinweise']));
    }

    public function test_ranking_determinismus(): void
    {
        $a = array_column($this->standard()->rankeKandidaten($this->eingabe())['kandidaten'], 'id');
        $b = array_column($this->standard()->rankeKandidaten($this->eingabe())['kandidaten'], 'id');
        $this->assertSame($a, $b);
    }
}
