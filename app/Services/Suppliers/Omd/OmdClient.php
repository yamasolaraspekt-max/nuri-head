<?php

namespace App\Services\Suppliers\Omd;

use App\Models\SupplierConnection;
use App\Models\SupplierImportLog;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * OMD (Open Masterdata) — REST-Katalog-Client. Drei Lookups:
 *   - product/bysupplierpid
 *   - product/bymanufacturerdata
 *   - product/bygtin
 *
 * base_url + timeout + datapackages kommen aus request_config. Defensive JSON-Auswertung:
 * fehlende Datenpakete = null (kein Throw); kaputtes JSON / Timeout / HTTP-Fehler = sauberer null-Rückgabewert
 * mit Log-Status 'failed'. Jeder Aufruf wird in supplier_import_logs protokolliert
 * (source_type='<connector>_return') — OHNE Zugangsdaten (kein Credential-Leak).
 *
 * K3: Auth-Token über OmdAuthService (Cache). Bei HTTP 401 wird das Token genau EINMAL erneuert und der
 * Request wiederholt; ein dritter Versuch findet nicht statt.
 */
class OmdClient
{
    /** Standard-Datenpakete, falls request_config.datapackages fehlt. */
    public const DATAPACKAGES = [
        'basic', 'additional', 'prices', 'descriptions',
        'logistics', 'sparepartlists', 'pictures', 'documents',
    ];

    public function __construct(private OmdAuthService $auth) {}

    public function bySupplierPid(SupplierConnection $connection, string $supplierPid, ?array $datapackages = null): ?array
    {
        return $this->lookup($connection, 'product/bysupplierpid', ['supplierPid' => $supplierPid], $datapackages);
    }

    public function byManufacturerData(SupplierConnection $connection, string $manufacturerName, string $manufacturerPid, ?array $datapackages = null): ?array
    {
        return $this->lookup($connection, 'product/bymanufacturerdata', [
            'manufacturerName' => $manufacturerName,
            'manufacturerPid' => $manufacturerPid,
        ], $datapackages);
    }

    public function byGtin(SupplierConnection $connection, string $gtin, ?array $datapackages = null): ?array
    {
        return $this->lookup($connection, 'product/bygtin', ['gtin' => $gtin], $datapackages);
    }

    /** Gemeinsamer Lookup mit Logging + 401-Retry. Gibt das dekodierte Paket-Array (fehlende Pakete = null) oder null. */
    private function lookup(SupplierConnection $connection, string $path, array $query, ?array $datapackages): ?array
    {
        $config = $connection->request_config ?? [];
        $baseUrl = rtrim((string) ($config['base_url'] ?? ''), '/');
        $timeout = (int) ($config['timeout'] ?? 30);
        $packages = array_values($datapackages ?? ($config['datapackages'] ?? self::DATAPACKAGES));
        $url = $baseUrl.'/'.ltrim($path, '/');

        // datapackage als Liste; KEINE Zugangsdaten im Query.
        $params = $query + ['datapackage' => $packages];

        $log = SupplierImportLog::create([
            'supplier_connection_id' => $connection->id,
            'status' => 'pending',
            'source_type' => ($connection->connector_type ?? 'omd').'_return',
            'started_at' => now(),
            'payload' => ['request' => ['path' => $path, 'query' => $params]],
        ]);

        try {
            $response = $this->sendWithRetry($connection, $url, $params, $timeout);
            $status = $response->status();

            // Produkt nicht gefunden: sauberer null-Fall, kein Fehler.
            if ($status === 404) {
                $log->update(['status' => 'success', 'finished_at' => now(), 'message' => 'not found (404)', 'total_items' => 0]);

                return null;
            }

            if (! $response->successful()) {
                $log->update(['status' => 'failed', 'finished_at' => now(), 'message' => 'HTTP '.$status]);

                return null;
            }

            // Defensive JSON-Auswertung: kaputtes JSON -> failed, kein Throw.
            $data = json_decode($response->body(), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $log->update(['status' => 'failed', 'finished_at' => now(), 'message' => 'invalid json']);

                return null;
            }

            $product = $this->extractPackages($data, $packages);

            $log->update([
                'status' => 'success',
                'finished_at' => now(),
                'total_items' => $product === null ? 0 : 1,
                'success_items' => $product === null ? 0 : 1,
                'payload' => array_merge($log->payload ?? [], ['response' => $data]),
            ]);

            return $product;
        } catch (ConnectionException $e) {
            $log->update(['status' => 'failed', 'finished_at' => now(), 'message' => 'connection/timeout']);

            return null;
        } catch (Throwable $e) {
            $log->update(['status' => 'failed', 'finished_at' => now(), 'message' => 'error']);

            return null;
        }
    }

    /** K3: Token holen, senden; bei 401 genau einmal forget + Re-Auth + Retry (kein dritter Versuch). */
    private function sendWithRetry(SupplierConnection $connection, string $url, array $params, int $timeout): Response
    {
        $token = $this->auth->getToken($connection);
        $response = Http::withToken($token)->timeout($timeout)->get($url, $params);

        if ($response->status() === 401) {
            $this->auth->forget($connection);
            $token = $this->auth->getToken($connection, true);
            $response = Http::withToken($token)->timeout($timeout)->get($url, $params);
        }

        return $response;
    }

    /**
     * Defensive Extraktion je Datenpaket: fehlendes Paket = null (kein Throw).
     * Gibt null zurück, wenn KEIN angefordertes Paket vorhanden ist (leeres/„not found"-Produkt).
     */
    private function extractPackages($data, array $packages): ?array
    {
        if (! is_array($data)) {
            return null;
        }

        // OMD kann das Produkt unter einem Wurzelknoten liefern; beide Formen defensiv abdecken.
        $root = (isset($data['product']) && is_array($data['product'])) ? $data['product'] : $data;

        $out = [];
        foreach ($packages as $package) {
            $out[$package] = $root[$package] ?? null;
        }

        $hasAny = array_filter($out, static fn ($value) => $value !== null) !== [];

        return $hasAny ? $out : null;
    }
}
