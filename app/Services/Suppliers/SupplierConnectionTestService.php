<?php

namespace App\Services\Suppliers;

use App\Models\SupplierConnection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SupplierConnectionTestService
{
    public function test(SupplierConnection $connection): array
    {
        try {
            if (!$connection->endpoint_url && !$connection->test_url) {
                return [
                    'success' => false,
                    'message' => 'Keine Shop-Adresse oder Test-Adresse vorhanden.',
                ];
            }

            $requestConfig = is_array($connection->request_config ?? null)
                ? $connection->request_config
                : [];

            $params = app(SupplierConnectorService::class)->buildOpenParams(
                connection: $connection,
                runtimeParams: [
                    'searchterm' => 'test',
                    'query' => 'test',
                    'auto' => false,
                    'uid' => auth()->id(),
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | Sonepar IDS/WKE special cleanup
            |--------------------------------------------------------------------------
            | Important:
            | - Do not touch GC Online
            | - Do not touch FEGA
            | - Only for Sonepar WKE remove non-standard IDS params.
            |--------------------------------------------------------------------------
            */
            $params = $this->normalizeParamsForTest($connection, $params);

            $requiredCheck = $this->validateRequiredParams($connection, $params);

            if (!$requiredCheck['success']) {
                return $requiredCheck;
            }

            $url = $connection->test_url ?: $connection->endpoint_url;
            $method = strtoupper((string) ($requestConfig['method'] ?? 'GET'));
            $timeout = (int) ($requestConfig['timeout'] ?? 20);
            $accept = (string) ($requestConfig['accept'] ?? 'text/html;charset=UTF-8');
            $contentType = strtolower((string) ($requestConfig['content_type'] ?? 'form'));

            $http = Http::timeout($timeout)
                ->accept($accept)
                ->withOptions([
                    'allow_redirects' => false,
                ]);

            if ($method === 'POST') {
                if ($contentType === 'multipart') {
                    $multipart = [];

                    foreach ($params as $key => $value) {
                        if ($value === null || $value === '') {
                            continue;
                        }

                        $multipart[] = [
                            'name' => (string) $key,
                            'contents' => is_array($value)
                                ? json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                                : (string) $value,
                        ];
                    }

                    $response = $http->send('POST', $url, [
                        'multipart' => $multipart,
                    ]);
                } else {
                    $response = $http->asForm()->post($url, $params);
                }
            } else {
                $response = $http->get($url, $params);
            }

            $body = mb_substr(strip_tags((string) $response->body()), 0, 700);

            if ($this->isGcOnline($connection) && $this->gcStillComplainsAboutParams($body)) {
                return [
                    'success' => false,
                    'message' => 'GC Online meldet weiterhin fehlende Login-Parameter. Gesendete Parameter: '
                        . implode(', ', array_keys($params))
                        . '. Prüfe, ob die Verbindung wirklich per POST gesendet wird und ob Passwort gespeichert ist. Antwort: '
                        . $body,
                ];
            }

            if (in_array($response->status(), [200, 201, 202, 301, 302, 303], true)) {
                return [
                    'success' => true,
                    'message' => 'Verbindung erreichbar. HTTP Status: '
                        . $response->status()
                        . '. Gesendete Parameter: '
                        . implode(', ', array_keys($params))
                        . $this->filledStatusText($params),
                ];
            }

            return [
                'success' => false,
                'message' => 'Verbindung erreichbar, aber unerwarteter HTTP Status: '
                    . $response->status()
                    . '. Gesendete Parameter: '
                    . implode(', ', array_keys($params))
                    . $this->filledStatusText($params)
                    . '. Antwort: '
                    . $body,
            ];
        } catch (\Throwable $exception) {
            Log::error('Supplier connection test failed', [
                'connection_id' => $connection->id,
                'supplier_key' => $connection->supplier_key,
                'error' => $exception->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Test fehlgeschlagen: ' . $exception->getMessage(),
            ];
        }
    }

    private function normalizeParamsForTest(SupplierConnection $connection, array $params): array
    {
        if (!$this->isSonepar($connection)) {
            return $params;
        }

        $action = strtoupper((string) ($params['action'] ?? $params['ACTION'] ?? ''));

        if ($action !== 'WKE') {
            return $params;
        }

        /*
        |--------------------------------------------------------------------------
        | IDS WKE = open shop and receive basket back.
        | searchterm, rueckurl and link are not standard WKE params.
        |--------------------------------------------------------------------------
        */
        unset(
            $params['searchterm'],
            $params['SEARCHTERM'],
            $params['query'],
            $params['search'],
            $params['rueckurl'],
            $params['RUECKURL'],
            $params['link'],
            $params['LINK']
        );

        return $params;
    }

    private function validateRequiredParams(SupplierConnection $connection, array $params): array
    {
        if (!$this->isGcOnline($connection)) {
            return [
                'success' => true,
                'message' => 'Parameter vollständig.',
            ];
        }

        $required = [
            'action',
            'version',
            'target',
            'kndnr',
            'name_kunde',
            'pw_kunde',
            'searchterm',
            'hookurl',
            'rueckurl',
        ];

        $missing = [];

        foreach ($required as $field) {
            if (!array_key_exists($field, $params) || $params[$field] === null || $params[$field] === '') {
                $missing[] = $field;
            }
        }

        if (!empty($missing)) {
            return [
                'success' => false,
                'message' => 'GC Online Parameter fehlen: '
                    . implode(', ', $missing)
                    . '. Gesendete Parameter: '
                    . implode(', ', array_keys($params))
                    . '. Prüfe Login-Parameter Mapping JSON und gespeicherte Zugangsdaten.',
            ];
        }

        return [
            'success' => true,
            'message' => 'GC Online Parameter vollständig.',
        ];
    }

    private function isGcOnline(SupplierConnection $connection): bool
    {
        $supplierKey = strtolower((string) $connection->supplier_key);
        $name = strtolower((string) $connection->name);
        $endpoint = strtolower((string) $connection->endpoint_url);

        return str_contains($supplierKey, 'gc')
            || str_contains($supplierKey, 'gc_online')
            || str_contains($name, 'gc online')
            || str_contains($name, 'gc-online')
            || str_contains($endpoint, 'gconline');
    }

    private function isSonepar(SupplierConnection $connection): bool
    {
        $supplierKey = strtolower((string) $connection->supplier_key);
        $name = strtolower((string) $connection->name);
        $endpoint = strtolower((string) $connection->endpoint_url);

        return str_contains($supplierKey, 'sonepar')
            || str_contains($name, 'sonepar')
            || str_contains($endpoint, 'sonepar.de');
    }

    private function gcStillComplainsAboutParams(string $body): bool
    {
        $body = mb_strtolower($body);

        return str_contains($body, 'name_kunde')
            || str_contains($body, 'pw_kunde')
            || (str_contains($body, 'parameter') && str_contains($body, 'fehlen'));
    }

    private function filledStatusText(array $params): string
    {
        $sensitiveKeys = [
            'password',
            'PASSWORD',
            'pw_kunde',
            'pass',
            'pw',
            'token',
            'TOKEN',
        ];

        $parts = [];

        foreach ($params as $key => $value) {
            if (
                in_array($key, $sensitiveKeys, true)
                || str_contains(strtolower((string) $key), 'pass')
                || str_contains(strtolower((string) $key), 'pw')
            ) {
                $parts[] = "{$key}=" . (filled($value) ? 'gefüllt' : 'LEER');
                continue;
            }

            $parts[] = "{$key}=" . (filled($value) ? 'gefüllt' : 'LEER');
        }

        return '. Werte: ' . implode(', ', $parts);
    }
}