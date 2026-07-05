<?php

namespace Tests\Feature\Security;

use App\Models\DealMeasurement;
use App\Models\RadiatorSpec;
use App\Models\User;
use App\Policies\DealMeasurementPolicy;
use Database\Seeders\AccessorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * S-1a — DealMeasurementPolicy (Deal-Zuständigkeit b+): Ownership-Matrix, Portal-Hart-Deny,
 * Waisen (weiches/hartes Deny), Backfill, Endpunkt-Enforcement.
 */
class DealMeasurementPolicyTest extends TestCase
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

    private function measurement(?int $dealEmp = 1, ?int $createdBy = null, ?int $responsible = null, bool $danglingDeal = false): DealMeasurement
    {
        return Schema::withoutForeignKeyConstraints(function () use ($dealEmp, $createdBy, $responsible, $danglingDeal) {
            if ($danglingDeal) {
                return DealMeasurement::create(['deal_id' => 999999, 'status' => 'draft', 'created_by' => $createdBy, 'responsible_employee_id' => $responsible]);
            }
            $dealId = DB::table('deals')->insertGetId([
                'customer_id' => 1, 'product_id' => 1, 'alternative_id' => 1, 'service' => 't',
                'employee_id' => $dealEmp, 'created_at' => now(), 'updated_at' => now(),
            ]);

            return DealMeasurement::create(['deal_id' => $dealId, 'status' => 'draft', 'created_by' => $createdBy, 'responsible_employee_id' => $responsible]);
        });
    }

    public function test_ersteller_techniker_und_deal_zustaendiger_duerfen(): void
    {
        $this->assertTrue($this->policy()->write($this->user('7'), $this->measurement(dealEmp: 5, createdBy: 7)));   // created_by
        $this->assertTrue($this->policy()->write($this->user('8'), $this->measurement(dealEmp: 5, responsible: 8))); // responsible
        $this->assertTrue($this->policy()->write($this->user('9'), $this->measurement(dealEmp: 9)));                 // deal.employee_id
    }

    public function test_unbeteiligter_employee_deny(): void
    {
        $this->assertFalse($this->policy()->write($this->user('999'), $this->measurement(dealEmp: 5)));
    }

    public function test_portal_ohne_employee_kontext_hart_deny(): void
    {
        // name nicht-numerisch -> employeeId() null -> immer deny (Portal-Fall)
        $this->assertFalse($this->policy()->write($this->user('portalkunde'), $this->measurement(dealEmp: 5)));
    }

    public function test_super_admin_darf(): void
    {
        $this->assertTrue($this->policy()->write($this->user('999', admin: true), $this->measurement(dealEmp: 5)));
    }

    public function test_waise_weiches_deny_wird_erlaubt_und_gezaehlt(): void
    {
        Cache::forget(DealMeasurementPolicy::ORPHAN_COUNTER);
        $waise = $this->measurement(danglingDeal: true); // deal null, created_by/responsible null -> kein Owner

        $this->assertTrue($this->policy()->write($this->user('123'), $waise)); // weich: erlaubt
        $this->assertSame(1, (int) Cache::get(DealMeasurementPolicy::ORPHAN_COUNTER));
    }

    public function test_waise_hartes_deny_bei_flag(): void
    {
        config(['features.deal_measurement_orphan_hard_deny' => true]);
        $waise = $this->measurement(danglingDeal: true);

        $this->assertFalse($this->policy()->write($this->user('123'), $waise));
    }

    public function test_backfill_setzt_created_by_aus_deal_employee(): void
    {
        $m = $this->measurement(dealEmp: 5, createdBy: null);
        $this->assertNull($m->created_by);

        Artisan::call('deal-measurements:backfill-owner');

        $this->assertEquals(5, (int) $m->fresh()->created_by);
    }

    public function test_endpunkt_enforcement_unbeteiligter_403(): void
    {
        $this->seed(AccessorySeeder::class);
        $spec = RadiatorSpec::create([
            'hersteller' => 'Kermi', 'typ' => '22', 'bauart' => 'kompakt', 'bauhoehe_mm' => 600, 'bautiefe_mm' => 100,
            'q_norm_w_pro_m' => 1666, 'norm_bedingung' => '75/65/20', 'exponent_n' => 1.30,
            'quelle' => 'test', 'imported_from' => 'test', 'aktiv' => true,
        ]);
        config(['features.heizkoerper' => true]);
        $m = $this->measurement(dealEmp: 5); // Owner = Employee 5

        // Unbeteiligter (Employee 999) -> 403 am Übernahme-Endpunkt
        $this->actingAs($this->user('999'))->postJson(route('heizkoerper.stueckliste.uebernehmen'), [
            'deal_measurement_id' => $m->id, 'section_title' => 'Wohnzimmer',
            'radiator_spec_id' => $spec->id, 'baulaenge_mm' => 1000, 'anzahl' => 1,
            'ist_ventil_heizkoerper' => false, 'kopf_norm_bestand' => 'M30x1_5',
        ])->assertForbidden();
    }
}
