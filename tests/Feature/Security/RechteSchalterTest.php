<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Z2-W0-7 — der Rechte-Schalter „alle für alle" (Yama 21.08.2026,
 * `docs/regelwerk/ENTSCHEIDUNG-RECHTE-ALLE-FUER-ALLE.md`).
 *
 * **Warum beide Stellungen geprüft werden, und warum das der Kern ist:** stünde der Schalter in der
 * Testumgebung auf `true`, würde er JEDEN Tor-Test grün färben — kein Test wüsste mehr, ob ein Tor
 * überhaupt schließt. Der Vorgabewert ist deshalb `false`; die `true`-Stellung wird hier
 * ausdrücklich je Test gesetzt und danach wieder verlassen.
 *
 * Geprüft wird an einer BESTANDS-Route (`hausplaner.index`, `permission:Hausplaner,read`), nicht an
 * einer eigens gebauten — eine Zusage über den Schalter ist nur so viel wert wie die Tore, an denen
 * er wirkt.
 *
 * Läuft ausschließlich gegen `ticket_testing` (`phpunit.xml` erzwingt `DB_DATABASE`).
 */
class RechteSchalterTest extends TestCase
{
    use DatabaseTransactions;

    private function nutzer(bool $admin = false): User
    {
        return User::factory()->create([
            'password' => 'password',
            'name' => (string) random_int(1, 9999),
            'is_admin' => $admin ? 1 : 0,
        ]);
    }

    private function grant(User $u, string $item, array $flags): void
    {
        DB::table('user_rolls')->insert(array_merge([
            'user_id' => $u->id, 'item_id' => $item,
            'is_read' => 0, 'is_add' => 0, 'is_update' => 0, 'is_delete' => 0,
            'created_at' => now(), 'updated_at' => now(),
        ], $flags));
    }

    /** Kriterium A — Schalter AUS: das Tor wirkt, in beide Richtungen. */
    public function test_schalter_aus_ohne_recht_verboten(): void
    {
        config(['rechte.alle_fuer_alle' => false]);
        $this->actingAs($this->nutzer())->get('/admin/hausplaner')->assertForbidden();
    }

    public function test_schalter_aus_mit_recht_erlaubt(): void
    {
        config(['rechte.alle_fuer_alle' => false]);
        $u = $this->nutzer();
        $this->grant($u, 'Hausplaner', ['is_read' => 1]);
        $this->assertNotSame(403, $this->actingAs($u)->get('/admin/hausplaner')->getStatusCode());
    }

    /** Kriterium B — Schalter AN: derselbe rechtelose Nutzer kommt durch. */
    public function test_schalter_an_ohne_recht_erlaubt(): void
    {
        config(['rechte.alle_fuer_alle' => true]);
        $this->assertNotSame(403, $this->actingAs($this->nutzer())->get('/admin/hausplaner')->getStatusCode());
    }

    /**
     * Kriterium B, zweite Hälfte — **der Schalter macht niemanden zum Admin.** Ohne diese Zusage
     * könnte jemand ihn später auf `is_admin` ausweiten und die Sonderpfade der Nutzerverwaltung
     * mitöffnen; das Entscheidungsblatt schließt das ausdrücklich aus.
     */
    public function test_schalter_an_macht_keinen_admin(): void
    {
        config(['rechte.alle_fuer_alle' => true]);
        $u = $this->nutzer();
        $this->assertTrue($u->hasPermission('Irgendwas', 'delete'), 'der Schalter wirkt');
        $this->assertFalse($u->isSuperAdmin(), 'aber er macht keinen Admin');
    }

    /** Kriterium C — das Item `Planner` ist nach der Migration referenzierbar. */
    public function test_item_planner_existiert(): void
    {
        $this->assertNotNull(
            DB::table('user_roll_items')->where('item', 'Planner')->first(),
            'Y-6: das Item Planner muss existieren, damit /planner/* es referenzieren kann',
        );
    }
}
