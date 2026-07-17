<?php

namespace Tests\Feature\Hausplaner;

use App\Domain\Hausplaner\Actions\UebernehmeSzeneInAuslegung;
use App\Models\LeadAlternativeAdd;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * P2-2 (Referenzfall) — Übernahme der Hausplaner-Szene in gebaeude_geometrie (append-only Profil-Version).
 * Grundlage: docs/planner-spec-p2-2-verdrahtung-szene-gebaeude-geometrie.md.
 *
 * Läuft gegen die Test-DB (RefreshDatabase); die Arbeits-/Dev-DB `ticket` wird NICHT geschrieben.
 * Muster (Objekt-Setup, FK-Checks) gespiegelt aus GrundrissProfilPersistenzTest.
 */
class UebernehmeSzeneInAuslegungTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement("SET SESSION sql_mode=''");
        config(['broadcasting.default' => 'null']);
    }

    private function objekt(int $seed = 200): int
    {
        $customer = $seed + 1;
        $alt = $seed + 2;
        DB::table('new_leads')->insert(['id' => $customer, 'customer_type' => 'privat', 'name' => 'K', 'lastname' => 'T', 'email' => 'k@example.com', 'phone' => '0', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('lead_alternative_adds')->insert(['id' => $alt, 'lead_id' => $customer, 'street' => 'Weg 1', 'postcode' => '12345', 'city' => 'S', 'created_at' => now(), 'updated_at' => now()]);

        return $alt;
    }

    /** @param array<string,mixed> $scene */
    private function dokument(int $alt, array $scene): void
    {
        DB::table('hausplaner_documents')->insert([
            'alternative_id' => $alt, 'schema_version' => 1, 'revision' => 1,
            'scene_json' => json_encode($scene), 'checksum' => 't',
            'created_by' => null, 'updated_by' => null, 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    /** @return array<string,mixed> */
    private function wall(string $id, int $sx, int $sy, int $ex, int $ey): array
    {
        return ['id' => $id, 'type' => 'wall', 'levelId' => 'L', 'start' => ['x' => $sx, 'y' => $sy], 'end' => ['x' => $ex, 'y' => $ey], 'height' => 2500];
    }

    /** Zwei angrenzende 5×5-Räume, geteilte Mittelwand. @return array<string,mixed> */
    private function zweiRaumSzene(): array
    {
        return [
            'levels' => [['id' => 'L', 'sortOrder' => 0, 'defaultWallHeight' => 2500]],
            'nodes' => [
                $this->wall('a_u', 0, 0, 5000, 0), $this->wall('b_u', 5000, 0, 10000, 0),
                $this->wall('re', 10000, 0, 10000, 5000), $this->wall('b_o', 10000, 5000, 5000, 5000),
                $this->wall('a_o', 5000, 5000, 0, 5000), $this->wall('li', 0, 5000, 0, 0),
                $this->wall('mid', 5000, 0, 5000, 5000),
            ],
        ];
    }

    private function action(): UebernehmeSzeneInAuslegung
    {
        return app(UebernehmeSzeneInAuslegung::class);
    }

    public function test_uebernahme_schreibt_neue_aktive_version_mit_2_raeumen(): void
    {
        $alt = $this->objekt();
        $this->dokument($alt, $this->zweiRaumSzene());

        $res = $this->action()->ausfuehren(LeadAlternativeAdd::findOrFail($alt), null);

        $this->assertSame('uebernommen', $res['status']);
        $this->assertSame(2, $res['raeume']);

        $profil = DB::table('anforderungsprofile')
            ->where('verankerbar_type', LeadAlternativeAdd::class)
            ->where('verankerbar_id', $alt)->where('status', 'aktiv')->first();
        $this->assertNotNull($profil);
        $geo = json_decode($profil->gebaeude_geometrie, true);
        $this->assertCount(2, $geo['raeume']);
        // S3: Herkunft/Hash unter reserviertem Key `_herkunft`.
        $this->assertSame('hausplaner_szene', $geo['_herkunft']['quelle']);
        $this->assertNotEmpty($geo['_herkunft']['source_hash']);
        // gebaeude_geometrie ist HeizlastRechner-Input: je Raum bauteile[] mit Wänden.
        $this->assertNotEmpty($geo['raeume'][0]['bauteile']);
        // S2-Ehrlichkeit: Geometrie-Übernahme markiert U-Werte als unbelegt und erfindet KEINEN belegten
        // u_wert (kein stiller Ersatzwert). Wand-Bauteile tragen u_strategie='C' ohne positiven u_wert.
        $this->assertSame('unbelegt', $geo['_herkunft']['u_werte']);
        $waende = array_filter($geo['raeume'][0]['bauteile'], fn ($b) => ($b['typ'] ?? null) === 'wand');
        $this->assertNotEmpty($waende);
        foreach ($waende as $b) {
            $this->assertSame('C', $b['u_strategie'] ?? null);
            $this->assertLessThanOrEqual(0.0, (float) ($b['u_wert'] ?? 0));
        }
    }

    public function test_zweite_uebernahme_gleiche_szene_keine_neue_version(): void
    {
        $alt = $this->objekt();
        $this->dokument($alt, $this->zweiRaumSzene());

        $this->action()->ausfuehren(LeadAlternativeAdd::findOrFail($alt), null);
        $res2 = $this->action()->ausfuehren(LeadAlternativeAdd::findOrFail($alt), null);

        $this->assertSame('unveraendert', $res2['status']);
        $this->assertSame(1, DB::table('anforderungsprofile')->where('verankerbar_id', $alt)->count()); // nur eine Version
        $this->assertSame(1, DB::table('anforderungsprofile')->where('verankerbar_id', $alt)->where('status', 'aktiv')->count());
    }

    public function test_keine_szene_schreibt_nichts(): void
    {
        $alt = $this->objekt();

        $res = $this->action()->ausfuehren(LeadAlternativeAdd::findOrFail($alt), null);

        $this->assertSame('keine_szene', $res['status']);
        $this->assertSame(0, DB::table('anforderungsprofile')->count());
    }

    public function test_szene_ohne_raum_schreibt_nichts(): void
    {
        $alt = $this->objekt();
        // Offener Wandzug → 0 Räume.
        $this->dokument($alt, [
            'levels' => [['id' => 'L', 'sortOrder' => 0, 'defaultWallHeight' => 2500]],
            'nodes' => [$this->wall('w1', 0, 0, 5000, 0), $this->wall('w2', 5000, 0, 5000, 5000), $this->wall('w3', 5000, 5000, 0, 5000)],
        ]);

        $res = $this->action()->ausfuehren(LeadAlternativeAdd::findOrFail($alt), null);

        $this->assertSame('kein_raum', $res['status']);
        $this->assertSame(0, DB::table('anforderungsprofile')->count());
    }
}
