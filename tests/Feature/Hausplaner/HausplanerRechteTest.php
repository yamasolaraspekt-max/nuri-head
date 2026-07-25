<?php

namespace Tests\Feature\Hausplaner;

use App\Http\Controllers\Hausplaner\HausplanerController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use ReflectionMethod;
use Tests\TestCase;

/**
 * AUF-64 / AUF-60 — `data-rechte` wird im **Controller** berechnet, nicht im Blade.
 *
 * **Warum der Umzug:** Im Blade brauchte die Berechnung einen `@php`-Block. Dessen schliessende
 * Marke hat sich, gepaart mit der einzeiligen Klammer-Form beim Uebernahme-Knopf, zur Rohblock-
 * Klammer verbunden — `objekt/203` lieferte einen PHP-ParseError. Im Controller kann das nicht
 * wiederkommen, und **die Berechnung ist pruefbar**; ein Block im Template ist es nicht.
 *
 * **Das wichtigste Kriterium ist der leere Fall:** ohne angemeldeten Nutzer muss die Liste **leer**
 * sein, nicht voll. Ein fehlender Nutzer darf nie „darf alles" bedeuten — das ist der Sinn, den
 * AUF-60 dem Attribut gibt, und er darf beim Umzug nicht verlorengehen.
 *
 * Laeuft gegen die Test-DB (RefreshDatabase); die Arbeits-DB `ticket` wird NICHT geschrieben.
 */
class HausplanerRechteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement("SET SESSION sql_mode=''");
        config(['broadcasting.default' => 'null']);
    }

    /**
     * Die Berechnung ist privat und soll es bleiben — sie ist kein Teil der Controller-Schnittstelle.
     * Geprueft wird sie trotzdem direkt: der Fall „kein Nutzer" ist ueber die Route gar nicht
     * erreichbar (dort steht `auth`), und genau dieser Fall ist der wichtigste.
     */
    private function rechteFuer(?User $nutzer): string
    {
        $methode = new ReflectionMethod(HausplanerController::class, 'hausplanerRechte');
        $methode->setAccessible(true);

        return $methode->invoke(app(HausplanerController::class), $nutzer);
    }

    public function test_ohne_angemeldeten_nutzer_bleibt_die_liste_leer(): void
    {
        $this->assertSame('', $this->rechteFuer(null), 'ein fehlender Nutzer darf nie „darf alles" heissen');
    }

    public function test_admin_bekommt_genau_die_vier_bekannten_rechte(): void
    {
        $admin = User::factory()->create(['password' => 'password', 'is_admin' => 1]);

        $this->assertSame(
            'Hausplaner,read Hausplaner,add Hausplaner,update Hausplaner,delete',
            $this->rechteFuer($admin),
            'Leerzeichen-getrennt — am Komma zu trennen zerlegte die Marken selbst',
        );
    }

    public function test_wer_nichts_darf_bekommt_nichts(): void
    {
        $ohne = User::factory()->create(['password' => 'password', 'is_admin' => 0]);

        $this->assertSame('', $this->rechteFuer($ohne), 'kein Recht in user_rolls ⇒ leere Liste');
    }

    /**
     * Nur die vier Aktionen, die `hasPermission()` wirklich kennt. Eine fuenfte („import") faellt
     * dort in den `default`-Zweig und damit auf `is_read` — sie stuende hier faelschlich als
     * eigenes Recht. AUF-53 hat genau deshalb auf `Hausplaner,add` abgebildet.
     */
    public function test_es_werden_keine_unbekannten_aktionen_erfunden(): void
    {
        $admin = User::factory()->create(['password' => 'password', 'is_admin' => 1]);
        $rechte = explode(' ', $this->rechteFuer($admin));

        $this->assertCount(4, $rechte);
        $this->assertNotContains('Hausplaner,import', $rechte);
    }

    /** Der Weg bis ins Markup: was der Controller rechnet, steht am Mount-Knoten. */
    public function test_die_objekt_seite_traegt_data_rechte_im_markup(): void
    {
        $admin = User::factory()->create(['password' => 'password', 'is_admin' => 1]);
        $objekt = $this->objekt();

        $antwort = $this->actingAs($admin)->get(route('hausplaner.objekt.seite', $objekt));

        $antwort->assertOk();
        $antwort->assertSee('data-rechte="Hausplaner,read Hausplaner,add Hausplaner,update Hausplaner,delete"', false);
    }

    /**
     * Die Gegenprobe zum Ausfall: die Seite laedt ueberhaupt. Vor dem Fix kam hier ein 500 mit
     * `ParseError: syntax error, unexpected token "class"`.
     */
    public function test_die_objekt_seite_laedt_und_ist_kein_parse_error(): void
    {
        $admin = User::factory()->create(['password' => 'password', 'is_admin' => 1]);

        $antwort = $this->actingAs($admin)->get(route('hausplaner.objekt.seite', $this->objekt()));

        $antwort->assertOk();
        $antwort->assertDontSee('ParseError', false);
        $antwort->assertSee('id="hausplaner-root"', false);
    }

    /** Ein Objekt (Kunde + lead_alternative_adds); Muster aus HausplanerIndexTest. */
    private function objekt(): int
    {
        DB::table('new_leads')->insert([
            'id' => 8101, 'customer_type' => 'privat', 'name' => 'V', 'lastname' => 'Rechte',
            'email' => 'rechte@example.com', 'phone' => '0', 'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('lead_alternative_adds')->insert([
            'id' => 8102, 'lead_id' => 8101, 'object_name' => 'Rechte-Objekt', 'street' => 'Weg 1',
            'postcode' => '12345', 'city' => 'S', 'created_at' => now(), 'updated_at' => now(),
        ]);

        return 8102;
    }
}
