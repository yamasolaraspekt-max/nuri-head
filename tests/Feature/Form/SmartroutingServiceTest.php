<?php

namespace Tests\Feature\Form;

use App\Services\Form\SmartroutingService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * FS-05 — Smartrouting: Anker→Formular, Spezifität vor Priorität, definierter Fallback.
 */
class SmartroutingServiceTest extends TestCase
{
    use DatabaseTransactions;

    private SmartroutingService $service;

    private int $agPv;

    private int $agWp;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SmartroutingService;
        $this->agPv = DB::table('article_groups')->insertGetId(['article_group' => 'PV', 'created_at' => now(), 'updated_at' => now()]);
        $this->agWp = DB::table('article_groups')->insertGetId(['article_group' => 'WP', 'created_at' => now(), 'updated_at' => now()]);
    }

    private function formular(int $articleGroupId): int
    {
        return DB::table('product_formulas')->insertGetId([
            'product_id' => $articleGroupId, 'section_name' => 'Sektion', 'fields' => '[]',
            'schema_version' => 2, 'status' => 'active', 'version' => 1, 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    private function regel(int $formulaId, array $anker, int $priority = 100): int
    {
        return DB::table('product_formula_routing_rules')->insertGetId(array_merge([
            'product_formula_id' => $formulaId, 'article_group_id' => null, 'lead_product_list_id' => null,
            'object_type' => null, 'priority' => $priority, 'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
        ], $anker));
    }

    public function test_spezifischere_regel_gewinnt_vor_wildcard(): void
    {
        $spezifisch = $this->formular($this->agPv);
        $fallback = $this->formular($this->agWp);
        $this->regel($spezifisch, ['article_group_id' => $this->agPv]); // Spezifität 1
        $this->regel($fallback, []);                                     // Wildcard, Spezifität 0

        $r = $this->service->route(['article_group_id' => $this->agPv]);
        $this->assertSame([$spezifisch, $fallback], $r['formula_ids']);
        $this->assertTrue($r['ambiguous']);
    }

    public function test_gesetzter_anker_muss_exakt_treffen(): void
    {
        $f = $this->formular($this->agPv);
        $this->regel($f, ['object_type' => 'efh']);

        $this->assertSame([], $this->service->route(['object_type' => 'mfh'])['formula_ids']);
        $this->assertSame([$f], $this->service->route(['object_type' => 'efh'])['formula_ids']);
    }

    public function test_gleichstand_entscheidet_prioritaet(): void
    {
        $niedrig = $this->formular($this->agPv);
        $hoch = $this->formular($this->agWp);
        $this->regel($niedrig, ['article_group_id' => $this->agPv], priority: 50);
        $this->regel($hoch, ['article_group_id' => $this->agPv], priority: 200);

        $r = $this->service->route(['article_group_id' => $this->agPv]);
        $this->assertSame([$hoch, $niedrig], $r['formula_ids']); // höhere Priorität zuerst
        $this->assertTrue($r['ambiguous']);
    }

    public function test_kein_treffer_liefert_leere_liste_und_hinweis(): void
    {
        $f = $this->formular($this->agPv);
        $this->regel($f, ['article_group_id' => $this->agPv]);

        $r = $this->service->route(['article_group_id' => 999]);
        $this->assertSame([], $r['formula_ids']);
        $this->assertFalse($r['ambiguous']);
        $this->assertStringContainsString('Kein Formular', $r['hinweis']);
    }

    public function test_inaktive_regel_wird_ignoriert(): void
    {
        $f = $this->formular($this->agPv);
        DB::table('product_formula_routing_rules')->insert([
            'product_formula_id' => $f, 'article_group_id' => $this->agPv, 'lead_product_list_id' => null,
            'object_type' => null, 'priority' => 100, 'is_active' => false, 'created_at' => now(), 'updated_at' => now(),
        ]);
        $this->assertSame([], $this->service->route(['article_group_id' => $this->agPv])['formula_ids']);
    }
}
