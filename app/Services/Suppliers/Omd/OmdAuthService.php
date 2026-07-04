<?php

namespace App\Services\Suppliers\Omd;

use App\Models\SupplierConnection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * OMD (Open Masterdata) — OAuth2 Password Grant.
 *
 * K3: Das Access-Token wird im Cache gehalten (Key omd_token:{connection_id}, TTL = expires_in - 60s
 * Puffer), NICHT in supplier_connections.token (das Feld bleibt statischen Händler-Tokens vorbehalten).
 *
 * Auth-Modus aus request_config.auth_mode:
 *   - 'username'        -> OAuth-Username = SupplierConnection.username
 *   - 'customer_number' -> OAuth-Username = SupplierConnection.customer_number
 *   - 'combined'        -> OAuth-Username = username . "\t" . customer_number (Reihenfolge zwingend)
 *
 * Zugangsdaten kommen ausschließlich aus den encrypted Feldern der SupplierConnection.
 * Es wird NICHTS geloggt (keine Credential-Leaks); Fehler werfen eine RuntimeException ohne Klartext-Creds.
 */
class OmdAuthService
{
    private const TTL_BUFFER_SECONDS = 60;

    public function cacheKey(SupplierConnection $connection): string
    {
        return 'omd_token:'.$connection->id;
    }

    /** Gültiges Access-Token aus Cache oder frisch geholt. */
    public function getToken(SupplierConnection $connection, bool $forceRefresh = false): string
    {
        $key = $this->cacheKey($connection);

        if (! $forceRefresh) {
            $cached = Cache::get($key);
            if (is_string($cached) && $cached !== '') {
                return $cached;
            }
        }

        [$token, $ttl] = $this->requestToken($connection);
        Cache::put($key, $token, max(1, $ttl));

        return $token;
    }

    /** Token verwerfen (K3: vor Re-Auth nach 401). */
    public function forget(SupplierConnection $connection): void
    {
        Cache::forget($this->cacheKey($connection));
    }

    /** Baut den OAuth-Username je nach auth_mode. */
    public function resolveAuthUsername(SupplierConnection $connection): string
    {
        $mode = $connection->request_config['auth_mode'] ?? 'username';
        $username = (string) ($connection->username ?? '');
        $customerNumber = (string) ($connection->customer_number ?? '');

        return match ($mode) {
            'customer_number' => $customerNumber,
            // Reihenfolge zwingend: username, TAB, customer_number
            'combined' => $username."\t".$customerNumber,
            default => $username,
        };
    }

    /** OAuth2 Password Grant. Gibt [access_token, ttl_seconds]. */
    private function requestToken(SupplierConnection $connection): array
    {
        $config = $connection->request_config ?? [];
        $authUrl = $config['auth_url'] ?? null;

        if (empty($authUrl)) {
            throw new RuntimeException('OMD: auth_url fehlt in request_config.');
        }

        $params = [
            'grant_type' => 'password',
            'username' => $this->resolveAuthUsername($connection),
            'password' => (string) ($connection->password ?? ''),
        ];

        // Optionale OAuth-Client-Parameter nur, wenn konfiguriert.
        foreach (['client_id', 'client_secret', 'scope'] as $optional) {
            if (! empty($config[$optional])) {
                $params[$optional] = $config[$optional];
            }
        }

        $timeout = (int) ($config['timeout'] ?? 30);
        $response = Http::asForm()->timeout($timeout)->post($authUrl, $params);

        if (! $response->successful()) {
            throw new RuntimeException('OMD: Token-Anfrage fehlgeschlagen (HTTP '.$response->status().').');
        }

        $data = $response->json();
        $token = is_array($data) ? ($data['access_token'] ?? null) : null;

        if (! is_string($token) || $token === '') {
            throw new RuntimeException('OMD: Kein access_token in der Token-Antwort.');
        }

        $expiresIn = (int) (is_array($data) ? ($data['expires_in'] ?? 3600) : 3600);

        return [$token, $expiresIn - self::TTL_BUFFER_SECONDS];
    }
}
