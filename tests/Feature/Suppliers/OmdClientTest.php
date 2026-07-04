<?php

namespace Tests\Feature\Suppliers;

use App\Models\SupplierConnection;
use App\Models\SupplierImportLog;
use App\Services\Suppliers\Omd\OmdAuthService;
use App\Services\Suppliers\Omd\OmdClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * OMD Phase 1 — OmdAuthService + OmdClient (additiv, kein Punchout-Pfad, K1–K3).
 * Vollständig über Http::fake; kein echter OMD-Endpunkt. Läuft gegen die isolierte Test-DB.
 */
class OmdClientTest extends TestCase
{
    use RefreshDatabase;

    private const AUTH_URL = 'https://omd.example/oauth/token';

    private const API = 'https://omd.example/api';

    private function makeConnection(array $configOverride = []): SupplierConnection
    {
        $distributorId = DB::table('distributors')->insertGetId([
            'status' => 1, 'created_at' => now(), 'updated_at' => now(),
        ]);

        return SupplierConnection::create([
            'distributor_id' => $distributorId,
            'name' => 'OMD Test',
            'supplier_key' => 'omd-test-'.$distributorId,
            'connector_type' => 'omd',
            'username' => 'user1',
            'password' => 'secret-pw',
            'customer_number' => 'CUST-99',
            'token' => 'static-tok',
            'request_config' => array_merge([
                'auth_mode' => 'username',
                'auth_url' => self::AUTH_URL,
                'base_url' => self::API,
                'timeout' => 15,
                'datapackages' => ['basic', 'prices', 'pictures'],
            ], $configOverride),
        ]);
    }

    private function client(): OmdClient
    {
        return new OmdClient(new OmdAuthService);
    }

    private function fakeOk(): void
    {
        Http::fake([
            self::AUTH_URL => Http::response(['access_token' => 'tok-1', 'expires_in' => 3600]),
            self::API.'/*' => Http::response(['product' => ['basic' => ['id' => 1], 'prices' => ['net' => 10]]], 200),
        ]);
    }

    /** @return \Illuminate\Http\Client\Request[] */
    private function recordedRequests(): array
    {
        /** @var array<int, array{0: \Illuminate\Http\Client\Request}> $recorded */
        $recorded = Http::recorded()->all();

        return array_map(static fn ($pair) => $pair[0], $recorded);
    }

    private function countRequests(string $needle): int
    {
        return count(array_filter(
            $this->recordedRequests(),
            static fn ($request) => str_contains($request->url(), $needle)
        ));
    }

    private function authUsernameSent(): string
    {
        foreach ($this->recordedRequests() as $request) {
            if (str_contains($request->url(), 'oauth/token')) {
                return (string) $request['username'];
            }
        }

        return '';
    }

    // 1
    public function test_auth_mode_username(): void
    {
        $this->fakeOk();
        $this->client()->bySupplierPid($this->makeConnection(['auth_mode' => 'username']), 'PID-1');

        $this->assertSame('user1', $this->authUsernameSent());
    }

    // 2
    public function test_auth_mode_customer_number(): void
    {
        $this->fakeOk();
        $this->client()->bySupplierPid($this->makeConnection(['auth_mode' => 'customer_number']), 'PID-1');

        $this->assertSame('CUST-99', $this->authUsernameSent());
    }

    // 3
    public function test_auth_mode_combined_tab_byte_exact(): void
    {
        $this->fakeOk();
        $this->client()->bySupplierPid($this->makeConnection(['auth_mode' => 'combined']), 'PID-1');

        // Reihenfolge zwingend: username, TAB (0x09), customer_number
        $this->assertSame("user1\tCUST-99", $this->authUsernameSent());
    }

    // 4
    public function test_token_is_cached_no_second_token_request(): void
    {
        $this->fakeOk();
        $conn = $this->makeConnection();
        $client = $this->client();

        $client->bySupplierPid($conn, 'PID-1');
        $client->byGtin($conn, '4001234567890');

        $this->assertSame(1, $this->countRequests('oauth/token'), 'Zweiter Lookup darf kein neues Token anfordern.');
    }

    // 5
    public function test_401_forgets_reauths_and_retries_exactly_once(): void
    {
        Http::fake([
            self::AUTH_URL => Http::sequence()
                ->push(['access_token' => 'tok-1', 'expires_in' => 3600])
                ->push(['access_token' => 'tok-2', 'expires_in' => 3600]),
            self::API.'/*' => Http::sequence()
                ->push(['message' => 'unauthorized'], 401)
                ->push(['product' => ['basic' => ['id' => 7]]], 200),
        ]);

        $result = $this->client()->bySupplierPid($this->makeConnection(), 'PID-1');

        $this->assertNotNull($result, 'Retry nach 401 muss erfolgreich sein.');
        $this->assertSame(2, $this->countRequests('/api/'), 'Genau ein Retry — kein dritter Versuch.');
        $this->assertSame(2, $this->countRequests('oauth/token'), 'Ein initiales Token + ein Re-Auth.');
    }

    // 6
    public function test_lookup_by_supplier_pid(): void
    {
        $this->fakeOk();
        $this->client()->bySupplierPid($this->makeConnection(), 'SPID-9', ['basic', 'prices']);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/api/product/bysupplierpid')
                && str_contains($request->url(), 'supplierPid=SPID-9')
                && str_contains($request->url(), 'basic')
                && str_contains($request->url(), 'prices');
        });
    }

    // 7
    public function test_lookup_by_manufacturer_data(): void
    {
        $this->fakeOk();
        $this->client()->byManufacturerData($this->makeConnection(), 'IMI', 'MPID-5', ['basic']);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/api/product/bymanufacturerdata')
                && str_contains($request->url(), 'manufacturerName=IMI')
                && str_contains($request->url(), 'manufacturerPid=MPID-5');
        });
    }

    // 8
    public function test_lookup_by_gtin(): void
    {
        $this->fakeOk();
        $this->client()->byGtin($this->makeConnection(), '4001234567890', ['basic']);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/api/product/bygtin')
                && str_contains($request->url(), 'gtin=4001234567890');
        });
    }

    // 9
    public function test_missing_datapackage_is_null_no_throw(): void
    {
        Http::fake([
            self::AUTH_URL => Http::response(['access_token' => 'tok-1', 'expires_in' => 3600]),
            self::API.'/*' => Http::response(['product' => ['basic' => ['id' => 1]]], 200), // keine 'pictures'
        ]);

        $result = $this->client()->bySupplierPid($this->makeConnection(), 'PID-1', ['basic', 'pictures']);

        $this->assertNotNull($result);
        $this->assertArrayHasKey('pictures', $result);
        $this->assertNull($result['pictures'], 'Fehlendes Paket muss null sein, kein Throw.');
        $this->assertNotNull($result['basic']);
    }

    // 10
    public function test_timeout_and_invalid_json_log_failed(): void
    {
        // Timeout
        Http::fake([
            self::AUTH_URL => Http::response(['access_token' => 'tok-1', 'expires_in' => 3600]),
            self::API.'/*' => fn () => throw new ConnectionException('timeout'),
        ]);
        $conn = $this->makeConnection();
        $this->assertNull($this->client()->bySupplierPid($conn, 'PID-1'));
        $this->assertSame('failed', SupplierImportLog::latest('id')->first()->status);

        // Kaputtes JSON (Token weiterhin gecacht)
        Http::fake([
            self::AUTH_URL => Http::response(['access_token' => 'tok-1', 'expires_in' => 3600]),
            self::API.'/*' => Http::response('das ist kein json {{{', 200),
        ]);
        $this->assertNull($this->client()->byGtin($conn, '4001234567890'));
        $this->assertSame('failed', SupplierImportLog::latest('id')->first()->status);
    }

    // 11
    public function test_no_credentials_leak_into_logs(): void
    {
        $this->fakeOk();
        $conn = $this->makeConnection();
        $this->client()->bySupplierPid($conn, 'PID-1');

        $secrets = ['user1', 'secret-pw', 'CUST-99', 'static-tok'];
        $logs = SupplierImportLog::all();
        $this->assertTrue($logs->isNotEmpty());

        foreach ($logs as $log) {
            $haystack = json_encode($log->payload).' '.(string) $log->message;
            foreach ($secrets as $secret) {
                $this->assertStringNotContainsString($secret, $haystack, "Credential-Leak im Log: {$secret}");
            }
        }
    }
}
