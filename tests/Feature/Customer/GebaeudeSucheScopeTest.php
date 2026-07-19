<?php

namespace Tests\Feature\Customer;

use App\Models\LeadAlternativeAdd;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Reuse-Fix (Befund 2 der Evaluator-Nachprüfung): die Gebäude-Suche war in
 * HausplanerController::index und Customer\ObjektakteController::index Wort für Wort dupliziert.
 * Sie liegt jetzt als EINE Wahrheit im Model-Scope LeadAlternativeAdd::scopeGebaeudeSuche.
 *
 * Dieser Test sichert den geteilten Scope direkt ab — und schützt damit BEIDE Aufrufer,
 * insbesondere den zuvor völlig ungetesteten ObjektakteController, dessen Suche jetzt
 * über den Scope läuft. Jede Filterkante (Straße/PLZ/Stadt/Objektname/Kunde) wird belegt,
 * ebenso der Leer-Begriff (kein Filter).
 *
 * Läuft gegen die Test-DB (RefreshDatabase); die Arbeits-DB `ticket` wird NICHT geschrieben.
 */
class GebaeudeSucheScopeTest extends TestCase
{
    use RefreshDatabase;

    private int $a;
    private int $b;

    protected function setUp(): void
    {
        parent::setUp();
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement("SET SESSION sql_mode=''");

        $this->a = $this->objekt(700, 'Villa-A', 'Ahornallee 1', '11111', 'Alphastadt', 'Zirkonium');
        $this->b = $this->objekt(710, 'Haus-B', 'Birkenweg 9', '22222', 'Betastadt', 'Xenon');
    }

    private function objekt(int $seed, string $name, string $street, string $plz, string $city, string $lastname): int
    {
        $customer = $seed + 1;
        $alt = $seed + 2;
        DB::table('new_leads')->insert([
            'id' => $customer, 'customer_type' => 'privat', 'name' => 'V', 'lastname' => $lastname,
            'email' => "k{$seed}@example.com", 'phone' => '0', 'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('lead_alternative_adds')->insert([
            'id' => $alt, 'lead_id' => $customer, 'object_name' => $name, 'street' => $street,
            'postcode' => $plz, 'city' => $city, 'created_at' => now(), 'updated_at' => now(),
        ]);

        return $alt;
    }

    /** @return array<int> */
    private function suche(?string $q): array
    {
        return LeadAlternativeAdd::query()->gebaeudeSuche($q)->orderBy('id')->pluck('id')->all();
    }

    public function test_leerer_begriff_filtert_nicht(): void
    {
        $this->assertSame([$this->a, $this->b], $this->suche(''));
    }

    public function test_nur_whitespace_filtert_nicht(): void
    {
        $this->assertSame([$this->a, $this->b], $this->suche('   '));
    }

    public function test_treffer_ueber_strasse(): void
    {
        $this->assertSame([$this->a], $this->suche('Ahornallee'));
    }

    public function test_treffer_ueber_postleitzahl(): void
    {
        $this->assertSame([$this->b], $this->suche('22222'));
    }

    public function test_treffer_ueber_stadt(): void
    {
        $this->assertSame([$this->a], $this->suche('Alphastadt'));
    }

    public function test_treffer_ueber_objektname(): void
    {
        $this->assertSame([$this->b], $this->suche('Haus-B'));
    }

    public function test_treffer_ueber_lead_relation(): void
    {
        // Kunde nur über die lead-Relation erreichbar — beweist den orWhereHas-Join im Scope.
        $this->assertSame([$this->a], $this->suche('Zirkonium'));
    }

    public function test_kein_treffer_gibt_leere_menge(): void
    {
        $this->assertSame([], $this->suche('Wolframstrasse-42-gibt-es-nicht'));
    }
}
