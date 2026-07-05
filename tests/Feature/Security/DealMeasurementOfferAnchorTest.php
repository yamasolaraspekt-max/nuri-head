<?php

namespace Tests\Feature\Security;

use App\Http\Controllers\Customer\Offer\DealMaterialListController;
use App\Models\Deal;
use App\Models\DealMeasurement;
use App\Models\OfferDetail;
use App\Models\User;
use App\Policies\DealMeasurementPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * S-1b-1 — W-0 (Deal-Anker schließt die Offer-Ebene der Material-Writer) + write-Gates auf Notizen/Bilder.
 */
class DealMeasurementOfferAnchorTest extends TestCase
{
    use RefreshDatabase;

    private function user(?string $name, bool $admin = false): User
    {
        return User::factory()->create(['password' => 'password', 'name' => $name ?? 'portal', 'is_admin' => $admin]);
    }

    private function deal(int $employeeId, int $offerId): void
    {
        Schema::withoutForeignKeyConstraints(function () use ($employeeId, $offerId) {
            DB::table('deals')->insert([
                'customer_id' => 1, 'product_id' => 1, 'alternative_id' => 1, 'service' => 't',
                'employee_id' => $employeeId, 'offer_id' => $offerId, 'created_at' => now(), 'updated_at' => now(),
            ]);
        });
    }

    private function offerDetail(int $offerId): OfferDetail
    {
        return Schema::withoutForeignKeyConstraints(fn () => OfferDetail::create([
            'offer_id' => $offerId, 'sections' => [], 'canvas_images' => [],
            'total_net' => 0, 'tax_rate' => 19, 'total_gross' => 0,
        ]));
    }

    private function measurement(int $dealEmp): DealMeasurement
    {
        return Schema::withoutForeignKeyConstraints(function () use ($dealEmp) {
            $dealId = DB::table('deals')->insertGetId([
                'customer_id' => 1, 'product_id' => 1, 'alternative_id' => 1, 'service' => 't',
                'employee_id' => $dealEmp, 'created_at' => now(), 'updated_at' => now(),
            ]);

            return DealMeasurement::create(['deal_id' => $dealId, 'status' => 'draft']);
        });
    }

    private function invokeAuthorize(OfferDetail $offerDetail): void
    {
        $controller = app(DealMaterialListController::class);
        $ref = new \ReflectionMethod($controller, 'authorizeMeasurementWrite');
        $ref->setAccessible(true);
        $ref->invoke($controller, $offerDetail);
    }

    public function test_deal_anker_gate_matrix(): void
    {
        $this->deal(employeeId: 5, offerId: 100);
        $deal = Deal::where('offer_id', 100)->first();

        $this->assertTrue(Gate::forUser($this->user('5'))->allows('write-deal-measurement-offer', $deal));       // Deal-Zuständiger
        $this->assertFalse(Gate::forUser($this->user('999'))->allows('write-deal-measurement-offer', $deal));    // Unbeteiligter
        $this->assertFalse(Gate::forUser($this->user('portalkunde'))->allows('write-deal-measurement-offer', $deal)); // Portal (kein Employee)
        $this->assertTrue(Gate::forUser($this->user('999', admin: true))->allows('write-deal-measurement-offer', $deal)); // Admin
    }

    public function test_w0_offer_ebene_zustaendiger_erlaubt(): void
    {
        $this->deal(employeeId: 7, offerId: 101);
        $od = $this->offerDetail(101);

        $this->actingAs($this->user('7'));
        $this->invokeAuthorize($od); // keine Exception
        $this->assertTrue(true);
    }

    public function test_w0_offer_ebene_unbeteiligter_403(): void
    {
        $this->deal(employeeId: 7, offerId: 102);
        $od = $this->offerDetail(102);

        $this->actingAs($this->user('999'));
        $this->expectException(\Illuminate\Auth\Access\AuthorizationException::class);
        $this->invokeAuthorize($od);
    }

    public function test_w0_kein_deal_deny_und_gezaehlt(): void
    {
        Cache::forget('offer_orphan_write_count');
        $od = $this->offerDetail(999); // kein Deal mit offer_id 999

        $this->actingAs($this->user('7'));
        try {
            $this->invokeAuthorize($od);
            $this->fail('erwartete 403 (kein Deal)');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            $this->assertSame(403, $e->getStatusCode());
        }
        $this->assertSame(1, (int) Cache::get('offer_orphan_write_count'));
    }

    public function test_storenote_enforcement(): void
    {
        $m = $this->measurement(dealEmp: 5);

        $this->actingAs($this->user('999'))->postJson(route('deal-measurements.notes.store', $m), ['note' => 'x'])->assertForbidden();
        $this->actingAs($this->user('portalkunde'))->postJson(route('deal-measurements.notes.store', $m), ['note' => 'x'])->assertForbidden();
        $this->assertNotSame(403, $this->actingAs($this->user('5'))->postJson(route('deal-measurements.notes.store', $m), ['note' => 'x'])->status());
    }

    public function test_image_upload_enforcement(): void
    {
        Storage::fake('local');
        $m = $this->measurement(dealEmp: 5);

        // Unbeteiligter -> 403 (Gate vor Datei-Verarbeitung)
        $this->actingAs($this->user('999'))
            ->postJson(route('deal-measurements.images.upload', $m), ['image' => UploadedFile::fake()->image('t.jpg')])
            ->assertForbidden();

        // Beteiligter -> Gate passiert (nicht 403; danach evtl. fachliche Meldung)
        $this->assertNotSame(403, $this->actingAs($this->user('5'))
            ->postJson(route('deal-measurements.images.upload', $m), ['image' => UploadedFile::fake()->image('t.jpg')])
            ->status());
    }
}
