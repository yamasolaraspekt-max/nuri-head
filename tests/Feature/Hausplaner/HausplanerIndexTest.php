<?php

namespace Tests\Feature\Hausplaner;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Scheibe 2 — Gebäude-Auswahl (Tools-Einstieg hausplaner.index).
 *
 * Sichert das Rechte-Gate (permission:Hausplaner,read) und den ?q=-Filter der neuen Route.
 * Vorher (Befund 1 der Evaluator-Nachprüfung): Route, 403-Gate und Filter waren NUR per
 * wieder entfernter Evaluator-Sonde belegt — im Repo lag kein Test. Entfernt jemand die
 * Middleware, schlägt ohne diese Tests nichts an.
 *
 * Läuft gegen die Test-DB (RefreshDatabase); die Arbeits-DB `ticket` wird NICHT geschrieben.
 */
class HausplanerIndexTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement("SET SESSION sql_mode=''");
        config(['broadcasting.default' => 'null']);
    }

    /** Legt ein Objekt (Kunde + lead_alternative_adds) an, gibt dessen alternative-id zurück. */
    private function objekt(int $seed, string $objectName, string $street, string $lastname): int
    {
        $customer = $seed + 1;
        $alt = $seed + 2;
        DB::table('new_leads')->insert([
            'id' => $customer, 'customer_type' => 'privat', 'name' => 'V', 'lastname' => $lastname,
            'email' => "k{$seed}@example.com", 'phone' => '0', 'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('lead_alternative_adds')->insert([
            'id' => $alt, 'lead_id' => $customer, 'object_name' => $objectName, 'street' => $street,
            'postcode' => '12345', 'city' => 'S', 'created_at' => now(), 'updated_at' => now(),
        ]);

        return $alt;
    }

    private function admin(): User
    {
        return User::factory()->create(['password' => 'password', 'is_admin' => 1]);
    }

    /** Gate: ohne Recht und nicht is_admin → 403 (das Muster, das uns drei Runden kostete). */
    public function test_index_ohne_recht_wird_403(): void
    {
        $user = User::factory()->create(['password' => 'password', 'is_admin' => 0]);

        $this->actingAs($user)->get('/admin/hausplaner')->assertStatus(403);
    }

    /** Gate: Admin → 200, Liste rendert das Objekt. */
    public function test_index_admin_listet(): void
    {
        $this->objekt(600, 'Ahornallee-Villa', 'Ahornallee 1', 'Zirkonium');

        $this->actingAs($this->admin())->get('/admin/hausplaner')
            ->assertOk()
            ->assertSee('Ahornallee-Villa');
    }

    /** Filter: q trifft über Straße/Objektname; der Nicht-Treffer verschwindet WIRKLICH. */
    public function test_index_q_filtert_treffer_und_nichttreffer(): void
    {
        $this->objekt(610, 'Ahornallee-Villa', 'Ahornallee 1', 'Zirkonium');
        $this->objekt(620, 'Birkenweg-Haus', 'Birkenweg 9', 'Xenon');

        $this->actingAs($this->admin())->get('/admin/hausplaner?q=Ahornallee')
            ->assertOk()
            ->assertSee('Ahornallee-Villa')
            ->assertDontSee('Birkenweg-Haus');
    }

    /** Filter: q trifft über die lead-Relation (Nachname) — der Scope-Join greift. */
    public function test_index_q_filtert_ueber_lead_relation(): void
    {
        $this->objekt(630, 'Ahornallee-Villa', 'Ahornallee 1', 'Zirkonium');
        $this->objekt(640, 'Birkenweg-Haus', 'Birkenweg 9', 'Xenon');

        $this->actingAs($this->admin())->get('/admin/hausplaner?q=Zirkonium')
            ->assertOk()
            ->assertSee('Ahornallee-Villa')
            ->assertDontSee('Birkenweg-Haus');
    }

    /** Zeilen-Link zeigt auf den persistenten Objekt-Planer (hausplaner.objekt.seite). */
    public function test_index_zeilenlink_zeigt_auf_objekt_seite(): void
    {
        $id = $this->objekt(650, 'Ahornallee-Villa', 'Ahornallee 1', 'Zirkonium');

        $this->actingAs($this->admin())->get('/admin/hausplaner')
            ->assertOk()
            ->assertSee("/admin/hausplaner/objekt/{$id}", false);
    }
}
