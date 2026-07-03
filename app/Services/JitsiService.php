<?php

namespace App\Services;

use App\Models\ChatGroup;
use App\Models\NewLeads;
use App\Models\User;
use App\Models\VideoCall;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

/**
 * Jitsi-Integration: Call anlegen, signierten Gast-Link bauen, optional HS256-JWT erzeugen.
 * KEINE neue Composer-Lib — JWT ist pures PHP (base64url + hash_hmac), nur aktiv bei jwt_enabled.
 */
class JitsiService
{
    public function createCall(NewLeads $customer, User $user): VideoCall
    {
        return VideoCall::create([
            'customer_id' => $customer->getKey(),
            'created_by' => $user->getKey(),
            'room_name' => $this->makeRoomName((int) $customer->getKey()),
            'status' => VideoCall::STATUS_CREATED,
        ]);
    }

    /**
     * Interner Call (Kollege ↔ Kollege). Genau eines von peer/group ist gesetzt
     * (Validierung hier, kein DB-Zwang). Kein Kunde, kein Gast-Zugang.
     */
    public function createInternalCall(User $user, ?User $peer, ?ChatGroup $group): VideoCall
    {
        if ((bool) $peer === (bool) $group) {
            throw new \InvalidArgumentException('Interner Call braucht genau EIN Ziel: peer ODER group.');
        }

        return VideoCall::create([
            'created_by' => $user->getKey(),
            'peer_user_id' => $peer?->getKey(),
            'chat_group_id' => $group?->getKey(),
            'room_name' => 'sa-intern-' . Str::random(32),
            'status' => VideoCall::STATUS_CREATED,
        ]);
    }

    /** Nicht erratbarer Raumname: sa-{customer_id}-{32 Zeichen}. */
    private function makeRoomName(int $customerId): string
    {
        return 'sa-' . $customerId . '-' . Str::random(32);
    }

    /**
     * Signierte, zeitlich begrenzte Gast-Route (TTL aus config).
     * Optionaler Gastname wird in die signierte URL aufgenommen (nicht manipulierbar) und
     * dient als displayName für Einladungs-Gäste (Leitplanke 3).
     */
    public function guestUrl(VideoCall $videoCall, ?string $guestName = null): string
    {
        $params = ['videoCall' => $videoCall->getKey()];
        if ($guestName !== null && trim($guestName) !== '') {
            $params['name'] = trim($guestName);
        }

        return URL::temporarySignedRoute(
            'video-call.guest',
            now()->addMinutes((int) config('jitsi.guest_link_ttl_minutes', 240)),
            $params
        );
    }

    /**
     * HS256-JWT nach Jitsi-Konvention. Nur wenn jwt_enabled + app_id/app_secret gesetzt sind,
     * sonst null (offener Raum). Gast = moderator:false, Mitarbeiter = moderator:true.
     */
    public function buildJwt(VideoCall $videoCall, string $displayName, ?string $email, bool $moderator): ?string
    {
        if (!config('jitsi.jwt_enabled')) {
            return null;
        }

        $appId = config('jitsi.app_id');
        $appSecret = config('jitsi.app_secret');
        if (empty($appId) || empty($appSecret)) {
            return null;
        }

        $now = time();
        $ttlSeconds = (int) config('jitsi.guest_link_ttl_minutes', 240) * 60;

        $header = ['alg' => 'HS256', 'typ' => 'JWT'];
        $payload = [
            'aud' => 'jitsi',
            'iss' => $appId,
            'sub' => config('jitsi.domain'),
            'room' => $videoCall->room_name,
            'iat' => $now,
            'nbf' => $now,
            'exp' => $now + $ttlSeconds,
            'context' => [
                'user' => [
                    'name' => $displayName,
                    'email' => $email,
                    'moderator' => $moderator,
                ],
            ],
        ];

        $segments = [
            $this->base64UrlEncode(json_encode($header)),
            $this->base64UrlEncode(json_encode($payload)),
        ];
        $signature = hash_hmac('sha256', implode('.', $segments), $appSecret, true);
        $segments[] = $this->base64UrlEncode($signature);

        return implode('.', $segments);
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
