<?php

namespace Tests\Feature\Form;

use App\Models\ProductFormula;
use App\Services\Form\FormSchemaValidator;
use Database\Seeders\WpProduktFormularSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Pilot Bereich 2 — WP-Formularinhalt aus playground lebt als v2 product_formula und validiert sauber.
 * (Reduziertes Ziel Option A; Render/Erfassung = Folgeposten F2, docs/bereich2-folgeposten.md.)
 */
class WpProduktFormularPilotTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Pilot prüft NUR den Formular-Import, nicht die article_groups-FK-Kette.
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
    }

    public function test_seeder_erzeugt_genau_eine_wp_product_formula_zeile(): void
    {
        $this->seed(WpProduktFormularSeeder::class);

        $this->assertSame(1, ProductFormula::count());

        $formula = ProductFormula::first();
        $this->assertSame(WpProduktFormularSeeder::WP_ARTICLE_GROUP_ID, (int) $formula->product_id);
        $this->assertSame('WP-Bedarf', $formula->section_name);
        $this->assertSame('playground:produkt_waermepumpe', $formula->imported_from);
        $this->assertSame(2, (int) $formula->schema_version);
        $this->assertSame('Published', $formula->status);
    }

    public function test_felder_sind_v2_schema_valide(): void
    {
        $fehler = (new FormSchemaValidator())->validate(WpProduktFormularSeeder::fields());
        $this->assertSame([], $fehler, 'Feld-Definition verletzt v2-Schema: '.implode(' | ', $fehler));

        // Auch die persistierte Zeile muss valide sein.
        $this->seed(WpProduktFormularSeeder::class);
        $persistiert = (new FormSchemaValidator())->validate(ProductFormula::first()->fields);
        $this->assertSame([], $persistiert);
    }

    public function test_18_felder_mit_eindeutigen_key_gleich_name_slugs(): void
    {
        $fields = WpProduktFormularSeeder::fields();

        $this->assertCount(18, $fields);

        $keys = [];
        foreach ($fields as $f) {
            // key = name (v1-Brücke), gültiger Slug, in Typ-Whitelist.
            $this->assertArrayHasKey('key', $f);
            $this->assertSame($f['key'], $f['name']);
            $this->assertMatchesRegularExpression('/^[a-z0-9_]+$/', $f['key']);
            $this->assertContains($f['type'], FormSchemaValidator::FIELD_TYPES);
            $keys[] = $f['key'];
        }

        $this->assertSame(count($keys), count(array_unique($keys)), 'Slugs müssen eindeutig sein.');
    }

    public function test_seeder_ist_idempotent(): void
    {
        $this->seed(WpProduktFormularSeeder::class);
        $this->seed(WpProduktFormularSeeder::class);

        $this->assertSame(1, ProductFormula::count());
    }

    public function test_import_haertung_schreibt_nichts_bei_ungueltigem_schema(): void
    {
        // Auswahltyp ohne options → v2-Schema verletzt → Import muss abbrechen, nichts schreiben.
        $ungueltig = [['key' => 'kaputt', 'label' => 'Kaputt', 'type' => 'select', 'options' => []]];

        try {
            (new WpProduktFormularSeeder())->importieren($ungueltig);
            $this->fail('Erwartete RuntimeException wegen v2-Schema-Verletzung.');
        } catch (\RuntimeException $e) {
            // erwartet
        }

        $this->assertSame(0, ProductFormula::count(), 'Bei ungültigem Schema darf nichts persistiert werden.');
    }
}
