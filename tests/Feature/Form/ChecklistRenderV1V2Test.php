<?php

namespace Tests\Feature\Form;

use App\Models\LeadProductChecklistValue;
use App\Models\ProductFormula;
use App\Models\User;
use Database\Seeders\WpProduktFormularSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * F2 — Checklisten-Blade/Controller v1+v2-fähig, ohne v1-Bestand zu brechen.
 * Prüft: v1 rendert, v2 rendert, v2 Save/Reload, multiselect, v1-CSV-Options, kein json_decode(array)-Fatal.
 */
class ChecklistRenderV1V2Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        DB::statement('SET FOREIGN_KEY_CHECKS=0'); // nur Render/Save-Logik im Fokus, nicht die Anker-FK-Kette
        config(['broadcasting.default' => 'null']);
    }

    private function render(ProductFormula $formula, array $filledValues = []): string
    {
        return view('admin.new_leads.checklists.checklist', [
            'formula' => $formula,
            'filled_values' => $filledValues,
        ])->render();
    }

    private function v1Formula(): ProductFormula
    {
        // v1: name-basiert, options als CSV-String, Alt-Typen.
        return ProductFormula::create([
            'product_id' => 2,
            'section_name' => 'V1-Bestand',
            'schema_version' => 1,
            'status' => 'Published',
            'fields' => [
                ['name' => 'v1_farbe', 'label' => 'Farbe', 'type' => 'select', 'options' => 'rot,gruen,blau'],
                ['name' => 'v1_notiz', 'label' => 'Notiz', 'type' => 'text'],
            ],
        ]);
    }

    public function test_v1_formular_rendert_weiter_mit_csv_options(): void
    {
        $html = $this->render($this->v1Formula());

        $this->assertStringContainsString('Farbe', $html);
        $this->assertStringContainsString('name="v1_farbe"', $html);
        // CSV-Options wurden zu echten <option> normalisiert.
        $this->assertStringContainsString('value="rot"', $html);
        $this->assertStringContainsString('value="gruen"', $html);
        $this->assertStringContainsString('name="v1_notiz"', $html);
    }

    public function test_v2_wp_pilot_rendert_mit_multiselect_und_typen(): void
    {
        $this->seed(WpProduktFormularSeeder::class);
        $formula = ProductFormula::where('imported_from', 'playground:produkt_waermepumpe')->firstOrFail();

        $html = $this->render($formula);

        // multiselect (wp_ziel) + Options-Label aus {value,label}-Array
        $this->assertStringContainsString('name="wp_ziel[]"', $html);
        $this->assertStringContainsString('multiple', $html);
        $this->assertStringContainsString('Heizung ersetzen', $html);
        // select-Label
        $this->assertStringContainsString('Einfamilienhaus', $html);
        // area (wp_wohnflaeche) → number-Input
        $this->assertStringContainsString('name="wp_wohnflaeche"', $html);
        $this->assertStringContainsString('type="number"', $html);
    }

    public function test_reload_zeigt_gespeicherte_werte_inkl_multiselect(): void
    {
        $this->seed(WpProduktFormularSeeder::class);
        $formula = ProductFormula::where('imported_from', 'playground:produkt_waermepumpe')->firstOrFail();

        $html = $this->render($formula, [
            'wp_ziel' => ['heizung_ersetzen', 'heizkosten_senken'], // multiselect als Array
            'wp_gebaeudeart' => 'efh',
        ]);

        // multiselect: beide Werte selektiert
        $this->assertMatchesRegularExpression('/value="heizung_ersetzen"[^>]*selected/', $html);
        $this->assertMatchesRegularExpression('/value="heizkosten_senken"[^>]*selected/', $html);
        // select: gespeicherter Wert selektiert
        $this->assertMatchesRegularExpression('/value="efh"[^>]*selected/', $html);
    }

    public function test_unbekannter_typ_faellt_auf_text_zurueck_ohne_crash(): void
    {
        $formula = ProductFormula::create([
            'product_id' => 2,
            'section_name' => 'Exotisch',
            'schema_version' => 2,
            'status' => 'Published',
            'fields' => [
                ['key' => 'x_unbekannt', 'name' => 'x_unbekannt', 'label' => 'Exot', 'type' => 'gibtsnicht'],
            ],
        ]);

        $html = $this->render($formula);
        $this->assertStringContainsString('name="x_unbekannt"', $html);
    }

    public function test_saveChecklist_persistiert_werte_inkl_multiselect(): void
    {
        $this->seedAnchors();
        $this->seed(WpProduktFormularSeeder::class);

        $user = User::factory()->create(['password' => 'password', 'name' => 'checklisttest', 'is_admin' => true]);

        $payload = [
            'lead_product_list_id' => 1,
            'customer_id' => 1,
            'alternative_id' => 1,
            'product_id' => 2,
            'filled_values' => [
                'wp_ziel' => ['heizung_ersetzen', 'heizkosten_senken'],
                'wp_gebaeudeart' => 'efh',
                'wp_wohnflaeche' => '160',
                'nicht_im_formular' => 'wird_gefiltert',
            ],
        ];

        $res = $this->actingAs($user)->postJson(route('lead-product-checklist.save'), $payload);
        $res->assertOk();

        $row = LeadProductChecklistValue::where('lead_product_list_id', 1)->firstOrFail();
        $werte = $row->filled_values; // array-cast

        $this->assertIsArray($werte['wp_ziel']);
        $this->assertEqualsCanonicalizing(['heizung_ersetzen', 'heizkosten_senken'], $werte['wp_ziel']);
        $this->assertSame('efh', $werte['wp_gebaeudeart']);
        $this->assertSame('160', $werte['wp_wohnflaeche']);
        // Feld, das nicht im Formular existiert, wird nach key??name gefiltert.
        $this->assertArrayNotHasKey('nicht_im_formular', $werte);
    }

    /**
     * Minimale Anker-Zeilen für die exists-Validierung. Strict-Mode nur für DIESE God-Table-Inserts
     * gelockert und danach WIEDER AKTIVIERT — der eigentliche saveChecklist-Insert in
     * lead_product_checklist_values läuft unter Strict-Mode (beweist, dass alle NOT-NULL-Felder gesetzt sind).
     */
    private function seedAnchors(): void
    {
        DB::statement("SET SESSION sql_mode=''");
        DB::table('article_groups')->insert(['id' => 2, 'article_group' => 'Wärmepumpe', 'initial' => 'WP', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('new_leads')->insert(['id' => 1, 'created_at' => now(), 'updated_at' => now()]);
        DB::table('lead_alternative_adds')->insert(['id' => 1, 'created_at' => now(), 'updated_at' => now()]);
        DB::table('lead_product_lists')->insert(['id' => 1, 'customer_id' => 1, 'product_id' => 2, 'created_at' => now(), 'updated_at' => now()]);
        DB::statement("SET SESSION sql_mode='STRICT_TRANS_TABLES'");
    }

    public function test_saveChecklist_v1_ueber_name_identity(): void
    {
        $this->seedAnchors();
        $this->v1Formula(); // schema_version=1, name-basiert (v1_farbe, v1_notiz)

        $user = User::factory()->create(['password' => 'password', 'name' => 'v1save', 'is_admin' => true]);

        $res = $this->actingAs($user)->postJson(route('lead-product-checklist.save'), [
            'lead_product_list_id' => 1,
            'customer_id' => 1,
            'alternative_id' => 1,
            'product_id' => 2,
            'filled_values' => ['v1_farbe' => 'gruen', 'v1_notiz' => 'Hallo'],
        ]);
        $res->assertOk();

        $row = LeadProductChecklistValue::where('lead_product_list_id', 1)->firstOrFail();
        // v1: Identität = name; Save (ohne vorherigen Init) füllt NOT-NULL-Snapshot/-Version.
        $this->assertSame('gruen', $row->filled_values['v1_farbe']);
        $this->assertSame('Hallo', $row->filled_values['v1_notiz']);
        $this->assertIsArray($row->formula_snapshot);
        $this->assertNotNull($row->formula_version);
    }
}
