<?php

namespace Tests\Feature\Geometrie;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * G0b / AP-4b — HTTP-Regressionstest des Ingestion-Gates (GrundrissController::vorschau/speichern).
 * Ungültiges Polygon über den echten HTTP-Pfad → abgelehnt (422 + Blocker), keine Persistenz,
 * kein Flächenwert. Gültige Geometrie wird NICHT vom Gate abgelehnt.
 */
class GrundrissGateHttpTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement("SET SESSION sql_mode=''");
        config(['broadcasting.default' => 'null']);
        $this->withoutMiddleware(VerifyCsrfToken::class);
    }

    private function admin(): User
    {
        return User::factory()->create(['password' => 'password', 'name' => 'gatetest', 'is_admin' => true]);
    }

    /** @return array<string,mixed> */
    private function payload(array $polygon): array
    {
        return [
            'hoehe_mm' => 2500,
            'polygon' => $polygon,
            'wand_segmente' => [
                ['von' => ['x' => 0, 'y' => 0], 'bis' => ['x' => 5000, 'y' => 0], 'grenzflaeche' => 'aussen'],
            ],
        ];
    }

    private function bowtie(): array
    {
        return [['x' => 0, 'y' => 0], ['x' => 5000, 'y' => 5000], ['x' => 5000, 'y' => 0], ['x' => 0, 'y' => 5000]];
    }

    private function quadrat(): array
    {
        return [['x' => 0, 'y' => 0], ['x' => 5000, 'y' => 0], ['x' => 5000, 'y' => 5000], ['x' => 0, 'y' => 5000]];
    }

    public function test_vorschau_lehnt_ungueltiges_polygon_ab(): void
    {
        $res = $this->actingAs($this->admin())
            ->postJson(route('energie.grundriss.vorschau'), $this->payload($this->bowtie()));

        $res->assertStatus(422);
        $ruleKeys = collect($res->json('blocker'))->pluck('rule_key')->all();
        $this->assertContains('selbstschnitt', $ruleKeys);
        // kein Flächenwert im Ablehnungsfall
        $this->assertNull($res->json('grundflaeche_m2'));
    }

    public function test_speichern_lehnt_ab_und_persistiert_nichts(): void
    {
        $vorher = DB::table('raum_geometrien')->count();

        $res = $this->actingAs($this->admin())
            ->postJson(route('energie.grundriss.speichern'), $this->payload($this->bowtie()));

        $res->assertStatus(422);
        $this->assertContains('selbstschnitt', collect($res->json('blocker'))->pluck('rule_key')->all());
        $this->assertSame($vorher, DB::table('raum_geometrien')->count(), 'Ungültige Geometrie wurde persistiert.');
        $this->assertSame(0, DB::table('heizlast_projekte')->count());
    }

    /** Autorisierungs-Angriff: der Ingestion-Endpunkt bleibt hinter Auth (kein anonymer Schreibversuch). */
    public function test_unauthentifiziert_wird_abgelehnt(): void
    {
        $res = $this->postJson(route('energie.grundriss.speichern'), $this->payload($this->quadrat()));
        $this->assertContains($res->status(), [401, 302], 'Ingestion-Endpunkt ist ohne Auth erreichbar.');
        $this->assertSame(0, DB::table('raum_geometrien')->count());
    }

    public function test_gueltige_geometrie_wird_vom_gate_nicht_abgelehnt(): void
    {
        $res = $this->actingAs($this->admin())
            ->postJson(route('energie.grundriss.vorschau'), $this->payload($this->quadrat()));

        // Das Gate lässt gültige Geometrie durch — es entsteht KEINE Gate-Ablehnung (kein 'blocker').
        // (Ob die nachgelagerte Heizlast-Rechnung 200 oder ein calc-422 liefert, hängt an Seed-Daten
        //  und ist NICHT Gegenstand von G0b.)
        $this->assertArrayNotHasKey('blocker', (array) $res->json());
    }
}
