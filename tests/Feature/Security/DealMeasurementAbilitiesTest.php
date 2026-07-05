<?php

namespace Tests\Feature\Security;

use App\Models\DealMeasurement;
use App\Models\Image;
use App\Models\User;
use App\Policies\DealMeasurementPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * S-1b-2 — enge Abilities assign/unlock/delete (+ Übergangs-Soft-Deny), Image-Lösch-Anker,
 * unlock 409 statt No-op, complete bleibt write.
 */
class DealMeasurementAbilitiesTest extends TestCase
{
    use RefreshDatabase;

    private function user(?string $name, bool $admin = false): User
    {
        return User::factory()->create(['password' => 'password', 'name' => $name ?? 'portal', 'is_admin' => $admin]);
    }

    private function policy(): DealMeasurementPolicy
    {
        return new DealMeasurementPolicy;
    }

    private function m(?int $dealEmp = 1, ?int $createdBy = null, ?int $responsible = null, string $status = 'draft'): DealMeasurement
    {
        return Schema::withoutForeignKeyConstraints(function () use ($dealEmp, $createdBy, $responsible, $status) {
            $dealId = DB::table('deals')->insertGetId([
                'customer_id' => 1, 'product_id' => 1, 'alternative_id' => 1, 'service' => 't',
                'employee_id' => $dealEmp, 'created_at' => now(), 'updated_at' => now(),
            ]);

            return DealMeasurement::create(['deal_id' => $dealId, 'status' => $status, 'created_by' => $createdBy, 'responsible_employee_id' => $responsible]);
        });
    }

    public function test_assign_deal_zustaendiger_ersteller_admin_ok_techniker_uebergang(): void
    {
        $p = $this->policy();
        $m = $this->m(dealEmp: 5, createdBy: 7, responsible: 9);

        $this->assertTrue($p->assign($this->user('5'), $m));                 // Deal-Zuständiger
        $this->assertTrue($p->assign($this->user('7'), $m));                 // Ersteller
        $this->assertTrue($p->assign($this->user('999', admin: true), $m));  // Admin

        Cache::forget('assign_denied_count');
        $this->assertTrue($p->assign($this->user('9'), $m));                 // Techniker (responsible): Übergang -> soft-allow
        $this->assertSame(1, (int) Cache::get('assign_denied_count'));

        config(['features.deal_measurement_assign_hard_deny' => true]);
        $this->assertFalse($p->assign($this->user('9'), $m));                // hart: Techniker-Selbstzuweisung -> deny

        $this->assertFalse($p->assign($this->user('123'), $m));              // Unbeteiligter
        $this->assertFalse($p->assign($this->user('portalx'), $m));          // Portal (kein Employee)
    }

    public function test_unlock_engster_kreis_uebergang(): void
    {
        $p = $this->policy();
        $m = $this->m(dealEmp: 5, createdBy: 7);

        $this->assertTrue($p->unlock($this->user('5'), $m));                 // Deal-Zuständiger
        $this->assertTrue($p->unlock($this->user('999', admin: true), $m));  // Admin

        Cache::forget('unlock_denied_count');
        $this->assertTrue($p->unlock($this->user('7'), $m));                 // Ersteller: nicht strict -> Übergang soft
        $this->assertSame(1, (int) Cache::get('unlock_denied_count'));

        config(['features.deal_measurement_unlock_hard_deny' => true]);
        $this->assertFalse($p->unlock($this->user('7'), $m));                // hart: Ersteller darf NICHT entsperren
        $this->assertFalse($p->unlock($this->user('999'), $m));              // Unbeteiligter
    }

    public function test_delete_ersteller_deal_zustaendiger_admin(): void
    {
        $p = $this->policy();
        $m = $this->m(dealEmp: 5, createdBy: 7, responsible: 9);

        $this->assertTrue($p->delete($this->user('7'), $m));                 // Ersteller
        $this->assertTrue($p->delete($this->user('5'), $m));                 // Deal-Zuständiger

        Cache::forget('delete_denied_count');
        $this->assertTrue($p->delete($this->user('9'), $m));                 // Techniker: Übergang soft
        $this->assertSame(1, (int) Cache::get('delete_denied_count'));

        $this->assertFalse($p->delete($this->user('999'), $m));              // Unbeteiligter
    }

    public function test_image_loesch_anker(): void
    {
        // Uploader
        $img = Schema::withoutForeignKeyConstraints(fn () => Image::create(['customer_id' => 100, 'created_by' => 7, 'image' => 'a.jpg', 'stage' => 'customer']));
        $this->assertTrue(Gate::forUser($this->user('7'))->allows('delete-measurement-image', $img));

        // Deal-Zuständiger des Kunden 100
        Schema::withoutForeignKeyConstraints(fn () => DB::table('deals')->insert([
            'customer_id' => 100, 'product_id' => 1, 'alternative_id' => 1, 'service' => 't',
            'employee_id' => 8, 'created_at' => now(), 'updated_at' => now(),
        ]));
        $img2 = Schema::withoutForeignKeyConstraints(fn () => Image::create(['customer_id' => 100, 'created_by' => 1, 'image' => 'b.jpg', 'stage' => 'customer']));
        $this->assertTrue(Gate::forUser($this->user('8'))->allows('delete-measurement-image', $img2));

        // Unbeteiligter / Portal -> deny
        $this->assertFalse(Gate::forUser($this->user('999'))->allows('delete-measurement-image', $img2));
        $this->assertFalse(Gate::forUser($this->user('portalx'))->allows('delete-measurement-image', $img2));
    }

    public function test_unlock_endpunkt_nicht_completed_409(): void
    {
        $m = $this->m(dealEmp: 5, status: 'draft');

        $this->actingAs($this->user('5'))
            ->postJson('/deal-measurements/' . $m->id . '/unlock')
            ->assertStatus(409);
    }

    public function test_complete_durch_techniker_bleibt_erlaubt(): void
    {
        $m = $this->m(dealEmp: 5, responsible: 9); // Techniker 9 = write-beteiligt

        $status = $this->actingAs($this->user('9'))
            ->postJson('/deal-measurements/' . $m->id . '/complete')
            ->status();

        $this->assertNotSame(403, $status); // complete = write -> Techniker weiter erlaubt
    }
}
