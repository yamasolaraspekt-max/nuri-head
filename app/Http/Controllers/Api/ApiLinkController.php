<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ApiLinkController extends Controller
{
    private string $clientId;
    private string $clientSecret;

    public function __construct()
    {
        $this->clientId = (string) config('services.myuplink.client_id');
        $this->clientSecret = (string) config('services.myuplink.client_secret');
    }

    private string $redirectUri = 'https://nuri-head.de/get/nibe/data';

    private string $authorizeUrl = 'https://login.myuplink.com/connect/authorize';
    private string $tokenUrl = 'https://login.myuplink.com/connect/token';
    private string $apiBaseUrl = 'https://api.myuplink.com/v2';

    public function redirectToAuth(Request $request)
    {
        $state = Str::random(40);

        session([
            'nibe_oauth_state' => $state,
        ]);

        $query = http_build_query([
            'client_id'     => $this->clientId,
            'response_type' => 'code',
            'redirect_uri'  => $this->redirectUri,
            'scope'         => 'openid offline_access READSYSTEM',
            'state'         => $state,
        ]);

        return redirect()->away($this->authorizeUrl . '?' . $query);
    }

    public function handleCallback(Request $request)
    {
        if ($request->has('error')) {
            return redirect('/nibe/devices')->with(
                'error',
                'NIBE login failed: ' . ($request->error_description ?: $request->error)
            );
        }

        if (!$request->filled('code')) {
            return redirect('/nibe/devices')->with('error', 'No authorization code was returned.');
        }

        if (!$request->filled('state') || $request->state !== session('nibe_oauth_state')) {
            return redirect('/nibe/devices')->with('error', 'Invalid OAuth state.');
        }

        $tokenResponse = Http::asForm()
            ->acceptJson()
            ->post($this->tokenUrl, [
                'grant_type'    => 'authorization_code',
                'code'          => $request->code,
                'redirect_uri'  => $this->redirectUri,
                'client_id'     => $this->clientId,
                'client_secret' => $this->clientSecret,
            ]);

        if ($tokenResponse->failed()) {
            Log::error('myUplink token exchange failed', [
                'status' => $tokenResponse->status(),
                'body'   => $tokenResponse->body(),
            ]);

            return redirect('/nibe/devices')->with(
                'error',
                'Failed to authenticate with myUplink: ' . $tokenResponse->status() . ' - ' . $tokenResponse->body()
            );
        }

        $tokens = $tokenResponse->json();

        session([
            'nibe_access_token'  => $tokens['access_token'] ?? null,
            'nibe_refresh_token' => $tokens['refresh_token'] ?? null,
            'nibe_token_type'    => $tokens['token_type'] ?? 'Bearer',
            'nibe_expires_in'    => $tokens['expires_in'] ?? null,
            'nibe_token_time'    => now()->timestamp,
        ]);

        return redirect('/nibe/devices')->with('success', 'Authenticated successfully.');
    }

    public function showDevices()
    {
        $accessToken = session('nibe_access_token');

        if (!$accessToken) {
            return redirect('/nibe/auth');
        }

        $response = $this->apiGet('/systems/me', $accessToken);

        if ($response->status() === 401) {
            $refreshed = $this->tryRefreshToken();

            if ($refreshed) {
                $accessToken = session('nibe_access_token');
                $response = $this->apiGet('/systems/me', $accessToken);
            }
        }

        if ($response->status() === 401) {
            return redirect('/nibe/auth')->with('error', 'Token expired. Please login again.');
        }

        if ($response->failed()) {
            Log::error('myUplink systems request failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return redirect('/nibe/auth')->with(
                'error',
                'API request failed: ' . $response->status() . ' - ' . $response->body()
            );
        }

        $data = $response->json();
        $devices = $this->extractDevices($data);

        return view('nibe.devices', [
            'devices' => $devices,
            'raw'     => $data,
        ]);
    }

    public function showDevice(string $deviceId)
    {
        $accessToken = session('nibe_access_token');

        if (!$accessToken) {
            return redirect('/nibe/auth');
        }

        $pointsResponse = $this->apiGet("/devices/{$deviceId}/points", $accessToken);

        if ($pointsResponse->status() === 401) {
            $refreshed = $this->tryRefreshToken();

            if ($refreshed) {
                $accessToken = session('nibe_access_token');
                $pointsResponse = $this->apiGet("/devices/{$deviceId}/points", $accessToken);
            }
        }

        if ($pointsResponse->failed()) {
            return redirect('/nibe/devices')->with(
                'error',
                'Failed to load device points: ' . $pointsResponse->status() . ' - ' . $pointsResponse->body()
            );
        }

        $points = $pointsResponse->json();

        return view('nibe.device', [
            'deviceId' => $deviceId,
            'points'   => $points,
        ]);
    }

    public function refreshToken()
    {
        if (!$this->tryRefreshToken()) {
            return redirect('/nibe/auth')->with('error', 'Failed to refresh token. Please login again.');
        }

        return redirect('/nibe/devices')->with('success', 'Token refreshed successfully.');
    }

    private function tryRefreshToken(): bool
    {
        $refreshToken = session('nibe_refresh_token');

        if (!$refreshToken) {
            return false;
        }

        $response = Http::asForm()
            ->acceptJson()
            ->post($this->tokenUrl, [
                'grant_type'    => 'refresh_token',
                'refresh_token' => $refreshToken,
                'client_id'     => $this->clientId,
                'client_secret' => $this->clientSecret,
            ]);

        if ($response->failed()) {
            Log::error('myUplink refresh token failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return false;
        }

        $tokens = $response->json();

        session([
            'nibe_access_token'  => $tokens['access_token'] ?? null,
            'nibe_refresh_token' => $tokens['refresh_token'] ?? $refreshToken,
            'nibe_token_type'    => $tokens['token_type'] ?? 'Bearer',
            'nibe_expires_in'    => $tokens['expires_in'] ?? null,
            'nibe_token_time'    => now()->timestamp,
        ]);

        return true;
    }

    private function apiGet(string $path, string $accessToken)
    {
        return Http::withToken($accessToken)
            ->acceptJson()
            ->timeout(20)
            ->get($this->apiBaseUrl . $path);
    }

    private function extractDevices(array $data): array
    {
        $devices = [];

        if (!empty($data['devices']) && is_array($data['devices'])) {
            foreach ($data['devices'] as $device) {
                $device['system_name'] = $device['system']['name'] ?? 'Unknown System';
                $devices[] = $device;
            }
        }

        if (!empty($data['systems']) && is_array($data['systems'])) {
            foreach ($data['systems'] as $system) {
                if (!empty($system['devices']) && is_array($system['devices'])) {
                    foreach ($system['devices'] as $device) {
                        $device['system_name'] = $system['name'] ?? 'Unknown System';
                        $devices[] = $device;
                    }
                }
            }
        }

        return collect($devices)
            ->filter(fn ($d) => !empty($d['id']))
            ->unique('id')
            ->values()
            ->all();
    }
}